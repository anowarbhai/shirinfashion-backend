<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = [
        'free_shipping_threshold',
        'free_shipping_enabled',
    ];

    protected $casts = [
        'free_shipping_threshold' => 'decimal:2',
        'free_shipping_enabled' => 'boolean',
    ];

    public static function getSettings()
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::create([
                'free_shipping_threshold' => 1000,
                'free_shipping_enabled' => true,
            ]);
        }
        return $settings;
    }

    public static function getFreeShippingThreshold()
    {
        $settings = self::getSettings();
        return $settings->free_shipping_enabled ? $settings->free_shipping_threshold : 0;
    }
}
