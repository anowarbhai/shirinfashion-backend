<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use App\Models\TaxSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function fraudChecker()
    {
        $settings = [
            'fraud_checker_enabled' => filter_var(config('app.fraud_checker_enabled') ?? env('FRAUD_CHECKER_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'fraud_checker_api_url' => config('app.fraud_checker_api_url') ?? env('FRAUD_CHECKER_API_URL', ''),
            'fraud_checker_api_key' => config('app.fraud_checker_api_key') ?? env('FRAUD_CHECKER_API_KEY', ''),
            'fraud_checker_pathao' => filter_var(config('app.fraud_checker_pathao') ?? env('FRAUD_CHECKER_PATHAO', true), FILTER_VALIDATE_BOOLEAN),
            'fraud_checker_redx' => filter_var(config('app.fraud_checker_redx') ?? env('FRAUD_CHECKER_REDX', true), FILTER_VALIDATE_BOOLEAN),
            'fraud_checker_carrybee' => filter_var(config('app.fraud_checker_carrybee') ?? env('FRAUD_CHECKER_CARRYBEE', true), FILTER_VALIDATE_BOOLEAN),
            'fraud_checker_steadfast' => filter_var(config('app.fraud_checker_steadfast') ?? env('FRAUD_CHECKER_STEADFAST', true), FILTER_VALIDATE_BOOLEAN),
        ];

        return view('admin.settings.fraud-checker', compact('settings'));
    }

    public function fraudCheckerUpdate(Request $request)
    {
        $this->saveSetting('fraud_checker_api_url', $request->input('fraud_checker_api_url', ''));
        $this->saveSetting('fraud_checker_api_key', $request->input('fraud_checker_api_key', ''));
        $this->saveSetting('fraud_checker_enabled', $request->has('fraud_checker_enabled') ? 'true' : 'false');
        $this->saveSetting('fraud_checker_pathao', $request->has('fraud_checker_pathao') ? 'true' : 'false');
        $this->saveSetting('fraud_checker_redx', $request->has('fraud_checker_redx') ? 'true' : 'false');
        $this->saveSetting('fraud_checker_carrybee', $request->has('fraud_checker_carrybee') ? 'true' : 'false');
        $this->saveSetting('fraud_checker_steadfast', $request->has('fraud_checker_steadfast') ? 'true' : 'false');

        return redirect()->route('admin.settings.fraud-checker')->with('success', 'Fraud Checker settings updated successfully!');
    }

    private function saveSetting($key, $value)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $keyString = strtoupper($key);
        $value = $value ?? '';

        // Quote values that contain spaces or special characters
        if (preg_match('/[\s\'"{}()\[\]$]/', $value)) {
            $value = '"'.str_replace('"', '\\"', $value).'"';
        }

        if (str_contains($envContent, $keyString.'=')) {
            $envContent = preg_replace(
                '/^'.$keyString.'=.*$/m',
                $keyString.'='.$value,
                $envContent
            );
        } else {
            $envContent .= "\n".$keyString.'='.$value;
        }

        file_put_contents($envPath, $envContent);
    }

    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $apiUrl = config('app.fraud_checker_api_url') ?? env('FRAUD_CHECKER_API_URL');
        $apiKey = config('app.fraud_checker_api_key') ?? env('FRAUD_CHECKER_API_KEY');

        if (! $apiUrl || ! $apiKey) {
            return response()->json(['error' => 'Fraud Checker API not configured'], 400);
        }

        try {
            $response = Http::get($apiUrl, [
                'key' => $apiKey,
                'phone' => $request->phone,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json(['error' => 'API request failed'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function productSettings()
    {
        // Review Settings
        $reviewSettings = [
            'global_reviews_enabled' => filter_var(config('app.global_reviews_enabled') ?? env('GLOBAL_REVIEWS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
            'global_avg_rating_enabled' => filter_var(config('app.global_avg_rating_enabled') ?? env('GLOBAL_AVG_RATING_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
            'guest_reviews_enabled' => filter_var(config('app.guest_reviews_enabled') ?? env('GUEST_REVIEWS_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        ];

        // Shipping Settings
        $shippingMethods = ShippingMethod::orderBy('sort_order')->get();
        $shippingSettings = ShippingSetting::getSettings();

        // Tax Settings
        $taxSettings = TaxSetting::getSettings();

        // Payment Methods (default for now)
        $paymentMethods = [
            ['id' => 1, 'name' => 'Cash on Delivery', 'is_active' => true],
            ['id' => 2, 'name' => 'Online Payment', 'is_active' => false],
        ];

        // Contact Settings
        $contactSettings = [
            'contact_buttons_enabled' => filter_var(config('app.contact_buttons_enabled') ?? env('CONTACT_BUTTONS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'whatsapp_number' => config('app.whatsapp_number') ?? env('WHATSAPP_NUMBER', ''),
            'call_number' => config('app.call_number') ?? env('CALL_NUMBER', ''),
            'whatsapp_message' => config('app.whatsapp_message') ?? env('WHATSAPP_MESSAGE', 'Hi, I\'m interested in this product: {product_name}. Please provide more details.'),
        ];

        return view('admin.settings.product-settings', compact(
            'reviewSettings',
            'shippingMethods',
            'shippingSettings',
            'taxSettings',
            'paymentMethods',
            'contactSettings'
        ));
    }

    public function productSettingsUpdate(Request $request)
    {
        $this->saveSetting('global_reviews_enabled', $request->has('global_reviews_enabled') ? 'true' : 'false');
        $this->saveSetting('global_avg_rating_enabled', $request->has('global_avg_rating_enabled') ? 'true' : 'false');
        $this->saveSetting('guest_reviews_enabled', $request->has('guest_reviews_enabled') ? 'true' : 'false');

        return redirect()->route('admin.settings.product')->with('success', 'Product settings updated successfully!');
    }

    public function shippingMethodStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $maxOrder = ShippingMethod::max('sort_order') ?? 0;

        ShippingMethod::create([
            'name' => $validated['name'],
            'cost' => $validated['cost'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.settings.product')->with('success', 'Shipping method created successfully!');
    }

    public function shippingMethodUpdate(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $shippingMethod->update([
            'name' => $validated['name'],
            'cost' => $validated['cost'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.settings.product')->with('success', 'Shipping method updated successfully!');
    }

    public function shippingMethodDestroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        return redirect()->route('admin.settings.product')->with('success', 'Shipping method deleted successfully!');
    }

    public function shippingMethodToggle(ShippingMethod $shippingMethod)
    {
        $shippingMethod->update(['is_active' => ! $shippingMethod->is_active]);

        return redirect()->route('admin.settings.product')->with('success', 'Shipping method status updated!');
    }

    public function shippingSettingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'free_shipping_threshold' => 'required|numeric|min:0',
        ]);

        $settings = ShippingSetting::first();
        if ($settings) {
            $settings->update([
                'free_shipping_threshold' => $validated['free_shipping_threshold'],
                'free_shipping_enabled' => $request->has('free_shipping_enabled'),
            ]);
        }

        return redirect()->route('admin.settings.product')->with('success', 'Shipping settings updated successfully!');
    }

    public function taxSettingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'tax_name' => 'required|string|max:255',
            'tax_type' => 'required|in:percentage,fixed',
            'tax_value' => 'required|numeric|min:0',
            'tax_price_type' => 'required|in:exclusive,inclusive',
        ]);

        $settings = TaxSetting::first();
        if ($settings) {
            $settings->update([
                'tax_enabled' => $request->has('tax_enabled'),
                'tax_name' => $validated['tax_name'],
                'tax_type' => $validated['tax_type'],
                'tax_value' => $validated['tax_value'],
                'tax_price_type' => $validated['tax_price_type'],
            ]);
        }

        return redirect()->route('admin.settings.product')->with('success', 'Tax settings updated successfully!');
    }

    public function contactSettingsUpdate(Request $request)
    {
        $this->saveSetting('contact_buttons_enabled', $request->has('contact_buttons_enabled') ? 'true' : 'false');
        $this->saveSetting('whatsapp_number', $request->input('whatsapp_number', ''));
        $this->saveSetting('call_number', $request->input('call_number', ''));
        $this->saveSetting('whatsapp_message', $request->input('whatsapp_message', 'Hi, I\'m interested in this product: {product_name}. Please provide more details.'));

        return redirect()->route('admin.settings.product')->with('success', 'Contact settings updated successfully!');
    }

    public function generalSettings()
    {
        $generalSettings = GeneralSetting::getSettings();

        return view('admin.settings.general', compact('generalSettings'));
    }

    public function generalSettingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'currency_symbol' => 'required|string|max:10',
            'currency_code' => 'required|string|max:10',
            'currency_position' => 'required|in:left,right',
            'timezone' => 'required|string|max:100',
            'date_format' => 'required|string|max:50',
            'time_format' => 'required|in:h:i A,H:i',
        ]);

        $settings = GeneralSetting::first();
        if ($settings) {
            $settings->update($validated);
        }

        return redirect()->route('admin.settings.general')->with('success', 'General settings updated successfully!');
    }
}
