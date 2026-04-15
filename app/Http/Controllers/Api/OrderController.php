<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use App\Models\TaxSetting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return $this->error('Unauthorized', 401);
        }

        $query = Order::with('items.product')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        return $this->success($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'shipping_method_id' => 'nullable|integer|exists:shipping_methods,id',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $validated['customer_phone']);
        $phoneWithoutCountryCode = preg_replace('/^(\+?88|0088)/', '', $phone);
        if (strlen($phoneWithoutCountryCode) < 11) {
            return $this->error('সঠিক মোবাইল নম্বর দিন (11 সংখ্যার)', 400);
        }

        $thirtyMinutesAgo = now()->subMinutes(30);

        // Check for incomplete orders and delete them (allow new order if found)
        $incompleteOrder = Order::whereRaw("REPLACE(REPLACE(REPLACE(customer_phone, '+88', ''), ' ', ''), '-', '') = ?", [$phone])
            ->where('status', 'incomplete')
            ->first();

        if ($incompleteOrder) {
            $incompleteOrder->items()->delete();
            $incompleteOrder->delete();
        }

        // Check for recent completed orders within 30 minutes
        $recentOrder = Order::whereRaw("REPLACE(REPLACE(REPLACE(customer_phone, '+88', ''), ' ', ''), '-', '') = ?", [$phone])
            ->whereIn('status', ['pending', 'processing', 'shipped', 'delivered'])
            ->where('created_at', '>=', $thirtyMinutesAgo)
            ->first();

        if ($recentOrder) {
            $minutesSinceOrder = intval(abs(now()->diffInMinutes($recentOrder->created_at)));
            $minutesLeft = max(1, 30 - $minutesSinceOrder);
            $minutesText = "{$minutesLeft} মিনিট";

            return $this->error("আপনি একই নম্বরে সম্প্রতি অর্ডার করেছেন। অনুগ্রহ করে {$minutesText} পরে আবার চেষ্টা করুন।", 429);
        }

        $userId = Auth::id();
        $sessionId = $request->header('X-Session-ID') ?? str()->uuid()->toString();

        // If not logged in, check if phone number matches a registered user
        if (! $userId) {
            $phone = preg_replace('/[^0-9]/', '', $validated['customer_phone']);
            // Try to find user by normalized phone number - exact match after normalization
            $user = User::whereRaw("REPLACE(REPLACE(REPLACE(phone, '+88', ''), ' ', ''), '-', '') = ?", [$phone])
                ->first();

            if ($user) {
                $userId = $user->id;
            }
        }

        // Get the original session ID from the header (before any user matching)
        $originalSessionId = $request->header('X-Session-ID');

        // Find cart items - check both user_id AND session_id to handle the case
        // where guest cart was created with session but user phone matches existing account
        $carts = Cart::with('product')
            ->where(function ($query) use ($userId, $sessionId, $originalSessionId) {
                if ($userId) {
                    // Check user_id OR session_id (for guest carts that get linked to user by phone)
                    $query->where('user_id', $userId);
                    if ($originalSessionId) {
                        $query->orWhere('session_id', $originalSessionId);
                    }
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();

        if ($carts->isEmpty()) {
            return $this->error('Cart is empty. Please add items to your cart and try again.', 400);
        }

        $subtotal = 0;
        $items = [];

        foreach ($carts as $cart) {
            $product = $cart->product;

            if (! $product || ! $product->is_active) {
                return $this->error("Product {$product->name} is no longer available", 400);
            }

            if ($product->stock_quantity < $cart->quantity) {
                return $this->error("Insufficient stock for {$product->name}", 400);
            }

            // Use cart's custom price if set (from volume discount), otherwise product price
            $price = $cart->price ?? $product->current_price;
            // Volume tier: subtotal = flat_price (direct). Regular: subtotal = price × quantity
            $itemSubtotal = $cart->volume_tier_id ? floatval($price) : floatval($price) * $cart->quantity;

            // Get volume tier info if set
            $volumeTierId = $cart->volume_tier_id;
            $volumeTierLabel = null;
            if ($volumeTierId && $product->volumeDiscounts) {
                $volumeTier = $product->volumeDiscounts->firstWhere('id', $volumeTierId);
                $volumeTierLabel = $volumeTier?->label;
            }

            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'price' => $price,
                'quantity' => $cart->quantity,
                'subtotal' => $itemSubtotal,
                'attributes' => $cart->attributes,
                'volume_tier_id' => $volumeTierId,
                'volume_tier_label' => $volumeTierLabel,
            ];

            $subtotal += $itemSubtotal;

            $product->decrement('stock_quantity', $cart->quantity);
        }

        // Calculate dynamic shipping cost
        $shippingMethodId = $validated['shipping_method_id'] ?? null;
        $shippingCost = 0;
        $deliveryMethod = null;

        if ($shippingMethodId) {
            $shippingMethod = ShippingMethod::find($shippingMethodId);
            if ($shippingMethod && $shippingMethod->is_active) {
                $deliveryMethod = $shippingMethod->name;
                $shippingCost = $shippingMethod->cost;

                // Apply free shipping threshold
                $settings = ShippingSetting::getSettings();
                if ($settings->free_shipping_enabled && $settings->free_shipping_threshold > 0) {
                    if ($subtotal >= $settings->free_shipping_threshold) {
                        $shippingCost = 0;
                    }
                }
            }
        }

        // Calculate tax dynamically
        $taxSettings = TaxSetting::getSettings();
        $tax = $taxSettings->calculateTax($subtotal);

        $couponCode = $validated['coupon_code'] ?? null;
        $discountAmount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
                $coupon->increment('usage_count');
            }
        }

        $total = $subtotal + $shippingCost + $tax - $discountAmount;

        $order = Order::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'tax_name' => $taxSettings->tax_enabled ? $taxSettings->tax_name : null,
            'tax_type' => $taxSettings->tax_enabled ? $taxSettings->tax_type : null,
            'tax_value' => $taxSettings->tax_enabled ? $taxSettings->tax_value : null,
            'tax_price_type' => $taxSettings->tax_enabled ? $taxSettings->tax_price_type : null,
            'discount' => $discountAmount,
            'coupon_code' => $couponCode,
            'total' => $total,
            'status' => 'processing',
            'payment_status' => 'pending',
            'payment_method' => $validated['payment_method'] ?? 'cash_on_delivery',
            'delivery_method' => $deliveryMethod,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Save customer rate data
        $rateData = $this->getCustomerRateData($validated['customer_phone']);
        if ($rateData) {
            $order->update([
                'customer_success_rate' => $rateData['success_rate'],
                'customer_cancel_rate' => $rateData['cancel_rate'],
                'customer_total_orders' => $rateData['total_orders'],
            ]);
        }

        // Delete incomplete orders older than 30 minutes with the same phone number
        $thirtyMinutesAgo = now()->subMinutes(30);
        Order::where('status', 'incomplete')
            ->whereRaw("REPLACE(REPLACE(REPLACE(customer_phone, '+88', ''), ' ', ''), '-', '') = ?", [$phone])
            ->where('created_at', '<', $thirtyMinutesAgo)
            ->delete();

        foreach ($items as $item) {
            OrderItem::create(array_merge($item, ['order_id' => $order->id]));
        }

        // Clear cart - delete items with both user_id and session_id
        Cart::where(function ($query) use ($userId, $sessionId, $originalSessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
                if ($originalSessionId) {
                    $query->orWhere('session_id', $originalSessionId);
                }
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        // Send SMS notification
        $smsService = new SmsService;
        $smsService->sendOrderPlacementSms($order);

        return $this->success($order->load('items'), 'Order placed successfully', 201);
    }

    public function show(Order $order)
    {
        $userId = Auth::id();
        $sessionId = request()->header('X-Session-ID');

        // Allow access if user is authenticated OR if order was placed with the same session
        if ($userId && $order->user_id !== $userId) {
            return $this->error('Unauthorized', 403);
        }

        // For guest orders, check session
        if (! $userId && $sessionId && $order->session_id !== $sessionId) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success($order->load('items.product'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'cancelled') {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
            }
        }

        // Send SMS notification
        $smsService = new SmsService;
        $smsService->sendOrderStatusSms($order);

        return $this->success($order, 'Order status updated');
    }

    public function saveIncomplete(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'shipping_method_id' => 'nullable|integer|exists:shipping_methods,id',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        // Only save incomplete order if phone number is at least 11 digits (excluding country code)
        if (empty($validated['customer_phone'])) {
            return $this->error('Phone number is required', 400);
        }

        $phone = preg_replace('/[^0-9]/', '', $validated['customer_phone']);
        $phone = preg_replace('/^(\+?88|0088)/', '', $phone);
        if (strlen($phone) < 11) {
            return $this->error('Invalid phone number', 400);
        }

        // Check for recent completed orders - don't save incomplete if order placed recently
        $thirtyMinutesAgo = now()->subMinutes(30);

        $recentOrder = Order::whereRaw("REPLACE(REPLACE(REPLACE(customer_phone, '+88', ''), ' ', ''), '-', '') = ?", [$phone])
            ->whereIn('status', ['pending', 'processing', 'shipped', 'delivered'])
            ->where('created_at', '>=', $thirtyMinutesAgo)
            ->first();

        if ($recentOrder) {
            return $this->error('আপনি সম্প্রতি একটি অর্ডার করেছেন।', 400);
        }

        $userId = Auth::id();
        $sessionId = $request->header('X-Session-ID') ?? str()->uuid()->toString();

        // Get cart items
        $originalSessionId = $request->header('X-Session-ID');
        $carts = Cart::with('product')
            ->where(function ($query) use ($userId, $sessionId, $originalSessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                    if ($originalSessionId) {
                        $query->orWhere('session_id', $originalSessionId);
                    }
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();

        if ($carts->isEmpty()) {
            return $this->error('Cart is empty', 400);
        }

        $subtotal = 0;
        $items = [];

        foreach ($carts as $cart) {
            $product = $cart->product;
            if (! $product || ! $product->is_active) {
                continue;
            }

            $price = $product->current_price;
            $itemSubtotal = $price * $cart->quantity;

            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'price' => $price,
                'quantity' => $cart->quantity,
                'subtotal' => $itemSubtotal,
                'attributes' => $cart->attributes,
            ];

            $subtotal += $itemSubtotal;
        }

        // Calculate shipping
        $shippingCost = 0;
        $deliveryMethod = null;
        if (! empty($validated['shipping_method_id'])) {
            $shippingMethod = ShippingMethod::find($validated['shipping_method_id']);
            if ($shippingMethod && $shippingMethod->is_active) {
                $deliveryMethod = $shippingMethod->name;
                $shippingCost = $shippingMethod->cost;
            }
        }

        // Calculate discount
        $discountAmount = 0;
        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', $validated['coupon_code'])->first();
            if ($coupon && $coupon->isValid()) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            }
        }

        // Calculate tax
        $taxSettings = TaxSetting::getSettings();
        $tax = $taxSettings->calculateTax($subtotal);

        $total = $subtotal + $shippingCost + $tax - $discountAmount;

        // Check if incomplete order already exists for this session
        $existingOrder = Order::where('session_id', $sessionId)
            ->where('status', 'incomplete')
            ->first();

        if ($existingOrder) {
            // Update existing incomplete order
            $existingOrder->update([
                'customer_name' => $validated['customer_name'] ?? $existingOrder->customer_name,
                'customer_email' => $validated['customer_email'] ?? $existingOrder->customer_email,
                'customer_phone' => $validated['customer_phone'] ?? $existingOrder->customer_phone,
                'shipping_address' => $validated['shipping_address'] ?? $existingOrder->shipping_address,
                'billing_address' => $validated['billing_address'] ?? $existingOrder->billing_address,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'tax_name' => $taxSettings->tax_enabled ? $taxSettings->tax_name : null,
                'tax_type' => $taxSettings->tax_enabled ? $taxSettings->tax_type : null,
                'tax_value' => $taxSettings->tax_enabled ? $taxSettings->tax_value : null,
                'discount' => $discountAmount,
                'coupon_code' => $validated['coupon_code'] ?? null,
                'total' => $total,
                'delivery_method' => $deliveryMethod,
                'notes' => $validated['notes'] ?? null,
                'payment_method' => $validated['payment_method'] ?? $existingOrder->payment_method,
            ]);

            // Delete old order items
            $existingOrder->items()->delete();

            // Create new order items
            foreach ($items as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $existingOrder->id]));
            }

            return $this->success($existingOrder->load('items'), 'Incomplete order updated');
        }

        // Create new incomplete order
        $order = Order::create([
            'user_id' => $userId,
            'session_id' => $userId ? null : $sessionId,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'tax_name' => $taxSettings->tax_enabled ? $taxSettings->tax_name : null,
            'tax_type' => $taxSettings->tax_enabled ? $taxSettings->tax_type : null,
            'tax_value' => $taxSettings->tax_enabled ? $taxSettings->tax_value : null,
            'tax_price_type' => $taxSettings->tax_enabled ? $taxSettings->tax_price_type : null,
            'discount' => $discountAmount,
            'coupon_code' => $validated['coupon_code'] ?? null,
            'total' => $total,
            'status' => 'incomplete',
            'payment_status' => 'pending',
            'payment_method' => $validated['payment_method'] ?? 'cash_on_delivery',
            'delivery_method' => $deliveryMethod,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Save customer rate data
        $rateData = $this->getCustomerRateData($validated['customer_phone']);
        if ($rateData) {
            $order->update([
                'customer_success_rate' => $rateData['success_rate'],
                'customer_cancel_rate' => $rateData['cancel_rate'],
                'customer_total_orders' => $rateData['total_orders'],
            ]);
        }

        foreach ($items as $item) {
            OrderItem::create(array_merge($item, ['order_id' => $order->id]));
        }

        return $this->success($order->load('items'), 'Incomplete order saved');
    }

    private function getCustomerRateData(string $phone): ?array
    {
        $apiUrl = config('app.fraud_checker_api_url');
        $apiKey = config('app.fraud_checker_api_key');
        if (! $apiUrl || ! $apiKey) {
            return null;
        }

        try {
            $client = new \GuzzleHttp\Client;
            $response = $client->get($apiUrl, [
                'query' => ['phone' => $phone, 'key' => $apiKey],
                'timeout' => 10,
            ]);
            $data = json_decode($response->getBody(), true);
            if ($data && isset($data['score'])) {
                $total = $data['total_parcel'] ?? 0;

                return [
                    'success_rate' => $data['score'] ?? 0,
                    'cancel_rate' => $total > 0 ? round(($data['cancel_parcel'] ?? 0) / $total * 100) : 0,
                    'total_orders' => $total,
                ];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
