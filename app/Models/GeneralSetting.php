<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'site_name',
        'currency_symbol',
        'currency_code',
        'currency_position',
        'timezone',
        'date_format',
        'time_format',
        'logo',
        'favicon',
    ];

    protected $appends = ['logo_url', 'favicon_url'];

    public static function getSettings()
    {
        $settings = self::first();
        if (! $settings) {
            $settings = self::create([
                'site_name' => 'Shirin Fashion',
                'currency_symbol' => '৳',
                'currency_code' => 'BDT',
                'currency_position' => 'left',
                'timezone' => 'Asia/Dhaka',
                'date_format' => 'M d, Y',
                'time_format' => 'h:i A',
            ]);
        }

        return $settings;
    }

    public static function formatCurrency($amount)
    {
        $settings = self::getSettings();
        $symbol = $settings->currency_symbol;
        $position = $settings->currency_position;

        $formatted = number_format($amount, 2);

        if ($position === 'right') {
            return $formatted.$symbol;
        }

        return $symbol.$formatted;
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
}
