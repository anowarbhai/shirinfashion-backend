<?php

namespace App\Http\Controllers\Api;

use App\Models\VolumeDiscount;
use Illuminate\Http\Request;

class VolumeDiscountController extends BaseController
{
    public function index(Request $request)
    {
        $productId = $request->product_id;

        $discounts = VolumeDiscount::with(['freeProduct:id,name,slug,image'])
            ->where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('quantity')
            ->get();

        return $this->success($discounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'tiers' => 'required|array|min:1',
            'tiers.*.quantity' => 'required|integer|min:1',
            'tiers.*.flat_price' => 'required|numeric|min:0',
            'tiers.*.label' => 'required|string|max:255',
            'tiers.*.free_product_id' => 'nullable|exists:products,id',
            'tiers.*.is_active' => 'boolean',
            'tiers.*.sort_order' => 'integer|min:0',
        ]);

        $productId = $validated['product_id'];

        // Delete ALL existing tiers first, then create new ones
        VolumeDiscount::where('product_id', $productId)->delete();

        // Create new tiers
        foreach ($validated['tiers'] as $tier) {
            VolumeDiscount::create([
                'product_id' => $productId,
                'quantity' => $tier['quantity'],
                'flat_price' => $tier['flat_price'],
                'label' => $tier['label'],
                'free_product_id' => $tier['free_product_id'] ?? null,
                'is_active' => $tier['is_active'] ?? true,
                'sort_order' => $tier['sort_order'] ?? 0,
            ]);
        }

        return $this->success(VolumeDiscount::where('product_id', $productId)->orderBy('quantity')->get(), 'Volume discounts saved successfully');
    }
}
