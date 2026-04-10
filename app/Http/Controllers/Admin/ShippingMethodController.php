<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::orderBy('sort_order')->get();
        $settings = ShippingSetting::getSettings();
        return view('admin.settings.shipping', compact('methods', 'settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $maxOrder = ShippingMethod::max('sort_order') ?? 0;
        
        ShippingMethod::create([
            'name' => $validated['name'],
            'cost' => $validated['cost'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.settings.shipping.index')->with('success', 'Shipping method created successfully!');
    }

    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $shippingMethod->update([
            'name' => $validated['name'],
            'cost' => $validated['cost'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.settings.shipping.index')->with('success', 'Shipping method updated successfully!');
    }

    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();
        return redirect()->route('admin.settings.shipping.index')->with('success', 'Shipping method deleted successfully!');
    }

    public function toggleStatus(ShippingMethod $shippingMethod)
    {
        $shippingMethod->update(['is_active' => !$shippingMethod->is_active]);
        return redirect()->route('admin.settings.shipping.index')->with('success', 'Shipping method status updated!');
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'free_shipping_threshold' => 'required|numeric|min:0',
            'free_shipping_enabled' => 'boolean',
        ]);

        $settings = ShippingSetting::first();
        if ($settings) {
            $settings->update([
                'free_shipping_threshold' => $validated['free_shipping_threshold'],
                'free_shipping_enabled' => $request->has('free_shipping_enabled'),
            ]);
        } else {
            ShippingSetting::create([
                'free_shipping_threshold' => $validated['free_shipping_threshold'],
                'free_shipping_enabled' => $request->has('free_shipping_enabled'),
            ]);
        }

        return redirect()->route('admin.settings.shipping.index')->with('success', 'Shipping settings updated successfully!');
    }
}
