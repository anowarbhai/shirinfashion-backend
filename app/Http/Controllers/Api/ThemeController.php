<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ThemeController extends BaseController
{
    public function index()
    {
        $settings = ThemeSetting::getSettings();

        $menus = Menu::with('allItems')->where('is_active', true)->get()->map(function ($menu) {
            return [
                'id' => $menu->id,
                'name' => $menu->name,
                'slug' => $menu->slug,
                'location' => $menu->location,
                'items' => $this->buildMenuTree($menu->allItems),
            ];
        });

        $data = [
            'logo' => $settings->logo ? asset('storage/'.$settings->logo) : null,
            'favicon' => $settings->favicon ? asset('storage/'.$settings->favicon) : null,
                'footer_logo' => $settings->footer_logo ? asset('storage/'.$settings->footer_logo) : null,
                'company_name' => $settings->company_name,
                'tagline' => $settings->tagline,
                'company_details' => $settings->company_details,
                'email' => $settings->email,
                'phone' => $settings->phone,
                'address' => $settings->address,
                'facebook' => $settings->facebook,
                'instagram' => $settings->instagram,
                'twitter' => $settings->twitter,
                'youtube' => $settings->youtube,
                'linkedin' => $settings->linkedin,
                'tiktok' => $settings->tiktok,
                'header_style' => $settings->header_style,
                'footer_style' => $settings->footer_style,
                'primary_menu' => $settings->primary_menu,
                'menus' => $menus,
                'header_styles' => ThemeSetting::getHeaderStyles(),
                'footer_styles' => ThemeSetting::getFooterStyles(),

// Marketing Settings
                'marketing' => [
                    // Facebook/Meta Pixel
                    'facebook_pixel_enabled' => filter_var(config('app.facebook_pixel_enabled', false), FILTER_VALIDATE_BOOLEAN),
                    'facebook_pixel_id' => config('app.facebook_pixel_id', ''),
                    'facebook_conversion_api_enabled' => filter_var(config('app.facebook_conversion_api_enabled', false), FILTER_VALIDATE_BOOLEAN),

                    // Google
                    'google_tag_manager_enabled' => filter_var(config('app.google_tag_manager_enabled', false), FILTER_VALIDATE_BOOLEAN),
                    'google_tag_manager_id' => config('app.google_tag_manager_id', ''),
                    'google_analytics_enabled' => filter_var(config('app.google_analytics_enabled', false), FILTER_VALIDATE_BOOLEAN),
                    'google_analytics_id' => config('app.google_analytics_id', ''),
                    'google_ads_enabled' => filter_var(config('app.google_ads_enabled', false), FILTER_VALIDATE_BOOLEAN),
                    'google_ads_id' => config('app.google_ads_id', ''),

                    // SEO
                    'seo_home_title' => config('app.seo_home_title', 'Shirin Fashion | Premium Cosmetics & Beauty'),
                    'seo_home_description' => config('app.seo_home_description', 'Discover premium cosmetics and beauty products at Shirin Fashion.'),
                    'seo_home_keywords' => config('app.seo_home_keywords', 'cosmetics, beauty, skincare, makeup'),
                ],
        ];

        return $this->success($data);
    }

    private function buildMenuTree($items, $parentId = null)
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $node = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'url' => $item->url,
                    'target' => $item->target,
                    'icon' => $item->icon,
                    'order' => $item->order,
                ];

                $children = $items->where('parent_id', $item->id);
                if ($children->count() > 0) {
                    $node['children'] = $this->buildMenuTree($items, $item->id);
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }

    public function favicon()
    {
        $settings = ThemeSetting::getSettings();

        if ($settings->favicon && Storage::disk('public')->exists($settings->favicon)) {
            $path = Storage::disk('public')->path($settings->favicon);
            $mimeType = mime_content_type($path);

            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // Return default favicon if no custom favicon is set
        $defaultFavicon = public_path('favicon.ico');
        if (file_exists($defaultFavicon)) {
            return response()->file($defaultFavicon, [
                'Content-Type' => 'image/x-icon',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        return response()->json(['message' => 'Favicon not found'], 404);
    }
}
