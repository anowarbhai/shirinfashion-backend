<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('id', 'like', "%{$request->search}%")
                    ->orWhere('order_number', 'like', "%{$request->search}%")
                    ->orWhere('customer_name', 'like', "%{$request->search}%")
                    ->orWhere('customer_email', 'like', "%{$request->search}%")
                    ->orWhere('customer_phone', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('is_active', 1)->where('stock_quantity', '>', 0)->get();
        $customers = User::where('is_admin', 0)->get();

        return view('admin.orders.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'required|string',
            'delivery_method' => 'required|string',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $orderNumber = 'ORD-'.strtoupper(Str::random(8));

        $subtotal = 0;
        $items = [];

        foreach ($validated['products'] as $item) {
            $product = Product::find($item['product_id']);
            $itemSubtotal = $product->price * $item['quantity'];
            $subtotal += $itemSubtotal;

            $items[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'price' => $product->price,
                'quantity' => $item['quantity'],
                'subtotal' => $itemSubtotal,
            ];

            // Decrease stock
            $product->decrement('stock_quantity', $item['quantity']);
        }

        $shippingCost = $validated['delivery_method'] === 'home_delivery' ? 100 : 0;
        $total = $subtotal + $shippingCost;

        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $request->filled('user_id') ? $request->user_id : null,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'delivery_method' => $validated['delivery_method'],
            'notes' => $validated['notes'],
        ]);

        foreach ($items as $item) {
            $order->items()->create($item);
        }

        // Send SMS notification
        $smsService = new SmsService;
        $smsService->sendOrderPlacementSms($order);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order created successfully');
    }

    public function show(Order $order)
    {
        $order->load('items.product');

        return view('admin.orders.show', compact('order'));
    }

    public function modal(Order $order)
    {
        $order->load('items.product');

        $generalSettings = \App\Models\GeneralSetting::getSettings();
        $currencySymbol = $generalSettings->currency_symbol ?? '৳';

        return response()->json([
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'customer_email' => $order->customer_email,
                'shipping_address' => $order->shipping_address,
                'subtotal' => number_format($order->subtotal, 2),
                'shipping_cost' => number_format($order->shipping_cost ?? 0, 2),
                'discount' => number_format($order->discount ?? 0, 2),
                'total' => number_format($order->total, 2),
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => number_format($item->price, 2),
                        'subtotal' => number_format($item->subtotal, 2),
                    ];
                })->toArray(),
            ],
            'currency_symbol' => $currencySymbol,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
            }
        }

        // Send SMS notification
        $smsService = new SmsService;
        $smsService->sendOrderStatusSms($order);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated');
    }

    public function destroy(Order $order)
    {
        // Restore stock quantities for cancelled orders
        if ($order->status === 'cancelled' || $order->status === 'incomplete') {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
            }
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:orders,id',
        ]);

        $orders = Order::whereIn('id', $validated['ids'])->get();

        foreach ($orders as $order) {
            // Restore stock quantities for cancelled orders
            if ($order->status === 'cancelled' || $order->status === 'incomplete') {
                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                }
            }
            $order->delete();
        }

        return redirect()->route('admin.orders.index')->with('success', count($orders).' order(s) deleted successfully');
    }

    public function updateRate(Request $request, Order $order)
    {
        $phone = $request->input('phone') ?? $order->customer_phone;

        $apiUrl = config('app.fraud_checker_api_url');
        $apiKey = config('app.fraud_checker_api_key');

        if (! $apiUrl || ! $apiKey) {
            return response()->json(['error' => 'API not configured'], 400);
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
                $cancelRate = $total > 0 ? round(($data['cancel_parcel'] ?? 0) / $total * 100) : 0;

                $order->update([
                    'customer_success_rate' => $data['score'],
                    'customer_cancel_rate' => $cancelRate,
                    'customer_total_orders' => $total,
                ]);

                return response()->json(['success' => true, 'rate' => $data['score']]);
            }

            return response()->json(['error' => 'No data returned'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
