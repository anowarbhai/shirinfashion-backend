<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketingController extends Controller
{
    // Facebook Settings
    public function facebook()
    {
        $settings = [
            'facebook_pixel_enabled' => filter_var(config('app.facebook_pixel_enabled') ?? env('FACEBOOK_PIXEL_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'facebook_pixel_id' => config('app.facebook_pixel_id') ?? env('FACEBOOK_PIXEL_ID', ''),
            'facebook_conversion_api_enabled' => filter_var(config('app.facebook_conversion_api_enabled') ?? env('FACEBOOK_CONVERSION_API_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'facebook_access_token' => config('app.facebook_access_token') ?? env('FACEBOOK_ACCESS_TOKEN', ''),
            'facebook_test_event_code' => config('app.facebook_test_event_code') ?? env('FACEBOOK_TEST_EVENT_CODE', ''),
        ];

        return view('admin.marketing.facebook', compact('settings'));
    }

    public function facebookUpdate(Request $request)
    {
        $validated = $request->validate([
            'facebook_pixel_id' => 'nullable|string|max:255',
            'facebook_access_token' => 'nullable|string',
            'facebook_test_event_code' => 'nullable|string|max:255',
        ]);

        $this->saveSetting('facebook_pixel_enabled', $request->has('facebook_pixel_enabled') ? 'true' : 'false');
        $this->saveSetting('facebook_pixel_id', $validated['facebook_pixel_id'] ?? '');
        $this->saveSetting('facebook_conversion_api_enabled', $request->has('facebook_conversion_api_enabled') ? 'true' : 'false');
        $this->saveSetting('facebook_access_token', $validated['facebook_access_token'] ?? '');
        $this->saveSetting('facebook_test_event_code', $validated['facebook_test_event_code'] ?? '');

        // Clear cache and config
        Cache::forget('marketing_settings');
        \Illuminate\Support\Facades\Artisan::call('config:clear');

        return redirect()->route('admin.marketing.facebook')->with('success', 'Facebook Conversion API settings updated successfully!');
    }

    // Google Settings
    public function google()
    {
        $settings = [
            'google_tag_manager_enabled' => filter_var(config('app.google_tag_manager_enabled') ?? env('GOOGLE_TAG_MANAGER_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'google_tag_manager_id' => config('app.google_tag_manager_id') ?? env('GOOGLE_TAG_MANAGER_ID', ''),
            'google_analytics_enabled' => filter_var(config('app.google_analytics_enabled') ?? env('GOOGLE_ANALYTICS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'google_analytics_id' => config('app.google_analytics_id') ?? env('GOOGLE_ANALYTICS_ID', ''),
            'google_ads_enabled' => filter_var(config('app.google_ads_enabled') ?? env('GOOGLE_ADS_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'google_ads_id' => config('app.google_ads_id') ?? env('GOOGLE_ADS_ID', ''),
        ];

        return view('admin.marketing.google', compact('settings'));
    }

    public function googleUpdate(Request $request)
    {
        $validated = $request->validate([
            'google_tag_manager_id' => 'nullable|string|max:255',
            'google_analytics_id' => 'nullable|string|max:255',
            'google_ads_id' => 'nullable|string|max:255',
        ]);

        $this->saveSetting('google_tag_manager_enabled', $request->has('google_tag_manager_enabled') ? 'true' : 'false');
        $this->saveSetting('google_tag_manager_id', $validated['google_tag_manager_id'] ?? '');
        $this->saveSetting('google_analytics_enabled', $request->has('google_analytics_enabled') ? 'true' : 'false');
        $this->saveSetting('google_analytics_id', $validated['google_analytics_id'] ?? '');
        $this->saveSetting('google_ads_enabled', $request->has('google_ads_enabled') ? 'true' : 'false');
        $this->saveSetting('google_ads_id', $validated['google_ads_id'] ?? '');

        // Clear cache and config
        Cache::forget('marketing_settings');
        \Illuminate\Support\Facades\Artisan::call('config:clear');

        return redirect()->route('admin.marketing.google')->with('success', 'Google Tag Manager & Analytics settings updated successfully!');
    }

    // SEO Settings
    public function seo()
    {
        $settings = [
            'seo_home_title' => config('app.seo_home_title') ?? env('SEO_HOME_TITLE', 'Shirin Fashion | Premium Cosmetics & Beauty'),
            'seo_home_description' => config('app.seo_home_description') ?? env('SEO_HOME_DESCRIPTION', 'Discover premium cosmetics and beauty products at Shirin Fashion. Shop skincare, makeup, fragrance and more.'),
            'seo_home_keywords' => config('app.seo_home_keywords') ?? env('SEO_HOME_KEYWORDS', 'cosmetics, beauty, skincare, makeup, fragrance, fashion'),
            'seo_robots_txt' => config('app.seo_robots_txt') ?? env('SEO_ROBOTS_TXT', "User-agent: *\nAllow: /\nSitemap: /sitemap.xml"),
        ];

        return view('admin.marketing.seo', compact('settings'));
    }

    public function seoUpdate(Request $request)
    {
        $validated = $request->validate([
            'seo_home_title' => 'required|string|max:255',
            'seo_home_description' => 'required|string|max:500',
            'seo_home_keywords' => 'required|string|max:500',
            'seo_robots_txt' => 'nullable|string',
        ]);

        $this->saveSetting('seo_home_title', $validated['seo_home_title']);
        $this->saveSetting('seo_home_description', $validated['seo_home_description']);
        $this->saveSetting('seo_home_keywords', $validated['seo_home_keywords']);
        $this->saveSetting('seo_robots_txt', $validated['seo_robots_txt'] ?? "User-agent: *\nAllow: /\nSitemap: /sitemap.xml");

        // Clear cache
        Cache::forget('marketing_settings');
        Cache::forget('theme_settings_api');

        return redirect()->route('admin.marketing.seo')->with('success', 'SEO settings updated successfully!');
    }

    private function saveSetting($key, $value)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        
        $keyString = strtoupper($key);
        $value = $value ?? '';
        
        // Handle multi-line values (like robots.txt)
        if (str_contains($value, "\n")) {
            $value = '"' . str_replace("\n", "\\n", $value) . '"';
        }
        
        if (str_contains($envContent, $keyString . '=')) {
            $envContent = preg_replace(
                '/^' . preg_quote($keyString, '/') . '=.*$/m',
                $keyString . '=' . $value,
                $envContent
            );
        } else {
            $envContent .= "\n" . $keyString . '=' . $value;
        }
        
        file_put_contents($envPath, $envContent);
        
        // Update config for current request
        config(['app.' . $key => $value]);
    }
}
