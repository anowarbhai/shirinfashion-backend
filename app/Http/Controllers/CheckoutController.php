<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        $carts = Cart::with('product')
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();

        if ($carts->isEmpty()) {
            return redirect('/api')->with('error', 'Your cart is empty');
        }

        $subtotal = 0;
        foreach ($carts as $cart) {
            if ($cart->product) {
                $subtotal += $cart->product->current_price * $cart->quantity;
            }
        }

        $shippingCost = $subtotal >= 5000 ? 0 : 150;
        $tax = $subtotal * 0.05;
        $total = $subtotal + $shippingCost + $tax;

        return view('checkout', compact('carts', 'subtotal', 'shippingCost', 'tax', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        $carts = Cart::with('product')
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();

        if ($carts->isEmpty()) {
            return back()->with('error', 'Cart is empty');
        }

        $subtotal = 0;
        $items = [];

        foreach ($carts as $cart) {
            $product = $cart->product;

            if (! $product || ! $product->is_active) {
                return back()->with('error', "Product {$product->name} is no longer available");
            }

            if ($product->stock_quantity < $cart->quantity) {
                return back()->with('error', "Insufficient stock for {$product->name}");
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
            ];

            $subtotal += $itemSubtotal;

            $product->decrement('stock_quantity', $cart->quantity);
        }

        $shippingCost = $subtotal >= 5000 ? 0 : 150;
        $tax = $subtotal * 0.05;
        $total = $subtotal + $shippingCost + $tax;

        $order = Order::create([
            'user_id' => $userId,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'discount' => 0,
            'total' => $total,
            'status' => 'processing',
            'payment_status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Assign moderator via round-robin
        $roundRobin = app(\App\Services\RoundRobinService::class);
        $assignedModerator = $roundRobin->assignOrder($order);
        \Log::info('Checkout: Order ' . $order->id . ' assigned to ' . ($assignedModerator?->name ?? 'NONE'));

        foreach ($items as $item) {
            OrderItem::create(array_merge($item, ['order_id' => $order->id]));
        }

        Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        return redirect('/api')->with('success', 'Order placed successfully! Order Number: '.$order->order_number);
    }
}
