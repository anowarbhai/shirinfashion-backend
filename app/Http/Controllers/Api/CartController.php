<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends BaseController
{
    private function getUserId(): ?int
    {
        return Auth::id();
    }

    private function getSessionId(): ?string
    {
        $sessionId = request()->header('X-Session-ID');

        if (! $sessionId) {
            // Log that no session ID was provided
            \Log::warning('No X-Session-ID header received', [
                'url' => request()->url(),
                'headers' => request()->headers->all(),
            ]);
            $sessionId = str()->uuid()->toString();
        }

        return $sessionId;
    }

    public function index()
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();

        $carts = Cart::with(['product' => function ($query) {
            $query->select('id', 'name', 'slug', 'image', 'price', 'sale_price', 'stock_quantity', 'is_active', 'category_id');
        }, 'product.volumeDiscounts' => function ($query) {
            $query->where('is_active', true)->orderBy('quantity');
        }])
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->get();

        $items = $carts->map(function ($cart) {
            if (! $cart->product) {
                return null;
            }

            // Use custom price if set (from volume discount), otherwise use product price
            $price = $cart->price ?? $cart->product->current_price;

            // Get volume tier if set
            $volumeTier = null;
            if ($cart->volume_tier_id && $cart->product->volumeDiscounts) {
                $volumeTier = $cart->product->volumeDiscounts->firstWhere('id', $cart->volume_tier_id);
            }

            return [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'attributes' => $cart->attributes,
                'volume_tier' => $volumeTier,
                'product' => [
                    'id' => $cart->product->id,
                    'name' => $cart->product->name,
                    'slug' => $cart->product->slug,
                    'image' => $cart->product->image,
                    'price' => $price,
                    'stock_quantity' => $cart->product->stock_quantity,
                ],
                'subtotal' => $cart->quantity * $price,
            ];
        })->filter();

        $total = $items->sum('subtotal');
        $count = $carts->sum('quantity');

        return $this->success([
            'items' => $items,
            'count' => $count,
            'total' => $total,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'attributes' => 'nullable|array',
            'attributes.*.id' => 'required|integer',
            'attributes.*.name' => 'required|string',
            'attributes.*.value' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'volume_tier_id' => 'nullable|integer',
        ]);

        // Use a simpler query to get only needed fields
        $product = Product::select('id', 'name', 'slug', 'image', 'price', 'sale_price', 'stock_quantity', 'is_active')
            ->findOrFail($validated['product_id']);

        if (! $product->is_active) {
            return $this->error('Product is not available', 400);
        }

        if ($product->stock_quantity < $validated['quantity']) {
            return $this->error('Insufficient stock', 400);
        }

        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        $attributes = $validated['attributes'] ?? null;

        // Check for existing cart item with same product AND same attributes
        $cart = Cart::where('product_id', $validated['product_id'])
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->when($attributes, function ($query) use ($attributes) {
                $query->where('attributes', json_encode($attributes));
            })
            ->when(! $attributes, function ($query) {
                $query->whereNull('attributes');
            })
            ->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $validated['quantity'];

            if ($product->stock_quantity < $newQuantity) {
                return $this->error('Insufficient stock for requested quantity', 400);
            }

            $cart->update([
                'quantity' => $newQuantity,
                'price' => $validated['price'] ?? null,
                'volume_tier_id' => $validated['volume_tier_id'] ?? null,
            ]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'attributes' => $attributes,
                'price' => $validated['price'] ?? null,
                'volume_tier_id' => $validated['volume_tier_id'] ?? null,
            ]);
        }

        // Get cart summary only (faster than full index)
        $cartSummary = $this->getCartSummary($userId, $sessionId);

        return $this->success($cartSummary);
    }

    private function getCartSummary(?int $userId, ?string $sessionId)
    {
        // Use a more efficient query
        $cartItems = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->with(['product' => function ($query) {
                $query->select('id', 'name', 'slug', 'image', 'price', 'sale_price', 'stock_quantity');
            }])
            ->get();

        $items = [];
        $total = 0;
        $count = 0;

        foreach ($cartItems as $cart) {
            if (! $cart->product) {
                continue;
            }

            $price = $cart->product->sale_price ?? $cart->product->price;
            $subtotal = $cart->quantity * $price;

            $items[] = [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'attributes' => $cart->attributes,
                'product' => [
                    'id' => $cart->product->id,
                    'name' => $cart->product->name,
                    'slug' => $cart->product->slug,
                    'image' => $cart->product->image,
                    'price' => $price,
                    'stock_quantity' => $cart->product->stock_quantity,
                ],
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
            $count += $cart->quantity;
        }

        return [
            'items' => $items,
            'count' => $count,
            'total' => $total,
        ];
    }

    public function update(Request $request, Cart $cart)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'volume_tier_id' => 'nullable|integer',
        ]);

        $product = $cart->product;

        if ($product->stock_quantity < $validated['quantity']) {
            return $this->error('Insufficient stock', 400);
        }

        $updateData = ['quantity' => $validated['quantity']];

        // Update price if volume tier changed
        if (isset($validated['price'])) {
            $updateData['price'] = $validated['price'];
        }
        if (isset($validated['volume_tier_id'])) {
            $updateData['volume_tier_id'] = $validated['volume_tier_id'];
        }

        $cart->update($updateData);

        return $this->success(null, 'Cart updated');
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        return $this->success(null, 'Item removed from cart');
    }

    public function clear()
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();

        Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        return $this->success(null, 'Cart cleared');
    }
}
