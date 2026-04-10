<?php

namespace App\Http\Controllers\Api;

use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use App\Models\TaxSetting;
use Illuminate\Http\Request;

class ShippingController extends BaseController
{
    public function index()
    {
        $methods = ShippingMethod::active()->get();
        $settings = ShippingSetting::getSettings();
        $taxSettings = TaxSetting::getSettings();

        return $this->success([
            'methods' => $methods,
            'free_shipping_threshold' => $settings->free_shipping_enabled ? $settings->free_shipping_threshold : null,
            'free_shipping_enabled' => $settings->free_shipping_enabled,
            'tax_enabled' => $taxSettings->tax_enabled,
            'tax_name' => $taxSettings->tax_name,
            'tax_type' => $taxSettings->tax_type,
            'tax_value' => $taxSettings->tax_value,
            'tax_price_type' => $taxSettings->tax_price_type ?? 'exclusive',
        ]);
    }

    public function calculateShipping(Request $request)
    {
        $subtotal = $request->input('subtotal', 0);
        $methodId = $request->input('method_id');

        $settings = ShippingSetting::getSettings();

        if (! $methodId) {
            return $this->error('Please select a shipping method', 400);
        }

        $method = ShippingMethod::find($methodId);

        if (! $method || ! $method->is_active) {
            return $this->error('Invalid shipping method', 400);
        }

        $shippingCost = $method->cost;

        // Apply free shipping if threshold is met
        if ($settings->free_shipping_enabled && $settings->free_shipping_threshold > 0) {
            if ($subtotal >= $settings->free_shipping_threshold) {
                $shippingCost = 0;
            }
        }

        return $this->success([
            'method_id' => $method->id,
            'method_name' => $method->name,
            'original_cost' => $method->cost,
            'shipping_cost' => $shippingCost,
            'free_shipping_applied' => $shippingCost == 0 && $method->cost > 0,
        ]);
    }
}
