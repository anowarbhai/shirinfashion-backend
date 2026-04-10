<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = [
        'logo',
        'favicon',
        'footer_logo',
        'company_name',
        'tagline',
        'company_details',
        'email',
        'phone',
        'address',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'linkedin',
        'tiktok',
        'header_style',
        'footer_style',
        'primary_menu',
    ];

    protected $appends = ['logo_url', 'favicon_url', 'footer_logo_url'];

    public static function getSettings()
    {
        $settings = self::first();
        if (! $settings) {
            $settings = self::create([
                'company_name' => 'Shirin Fashion',
                'header_style' => 'style1',
                'footer_style' => 'style1',
                'primary_menu' => 'main',
            ]);
        }

        return $settings;
    }

    // Header styles configuration
    public static function getHeaderStyles()
    {
        return [
            'style1' => [
                'name' => 'Classic Header',
                'description' => 'Logo left, navigation center, icons right',
                'preview' => '/images/headers/style1.jpg',
            ],
            'style2' => [
                'name' => 'Modern Header',
                'description' => 'Full-width with centered logo and mega menu',
                'preview' => '/images/headers/style2.jpg',
            ],
            'style3' => [
                'name' => 'Minimal Header',
                'description' => 'Clean layout with hamburger menu',
                'preview' => '/images/headers/style3.jpg',
            ],
        ];
    }

    // Footer styles configuration
    public static function getFooterStyles()
    {
        return [
            'style1' => [
                'name' => 'Classic Footer',
                'description' => 'Four columns with newsletter signup',
                'preview' => '/images/footers/style1.jpg',
            ],
            'style2' => [
                'name' => 'Modern Footer',
                'description' => 'Simple two-column layout',
                'preview' => '/images/footers/style2.jpg',
            ],
            'style3' => [
                'name' => 'Minimal Footer',
                'description' => 'Copyright only with social links',
                'preview' => '/images/footers/style3.jpg',
            ],
        ];
    }

    public function getLogoUrlAttribute()
    {
        if (! $this->logo) {
            return null;
        }

        $logo = $this->logo;
        $logo = ltrim($logo, '/');
        if (strpos($logo, 'storage/') === 0) {
            $logo = substr($logo, 8);
        }

        return asset('storage/'.$logo);
    }

    public function getFaviconUrlAttribute()
    {
        if (! $this->favicon) {
            return null;
        }

        $favicon = $this->favicon;
        $favicon = ltrim($favicon, '/');
        if (strpos($favicon, 'storage/') === 0) {
            $favicon = substr($favicon, 8);
        }

        return asset('storage/'.$favicon);
    }

    public function getFooterLogoUrlAttribute()
    {
        if (! $this->footer_logo) {
            return null;
        }

        $logo = $this->footer_logo;
        $logo = ltrim($logo, '/');
        if (strpos($logo, 'storage/') === 0) {
            $logo = substr($logo, 8);
        }

        return asset('storage/'.$logo);
    }
}
