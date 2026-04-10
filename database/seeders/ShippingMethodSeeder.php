<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use App\Models\ShippingSetting;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Inside Dhaka',
                'cost' => 60,
                'description' => 'Delivery within 2-3 days inside Dhaka city',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Outside Dhaka',
                'cost' => 150,
                'description' => 'Delivery within 4-5 days outside Dhaka',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Express Delivery',
                'cost' => 200,
                'description' => 'Same day delivery within Dhaka',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Pickup Point',
                'cost' => 0,
                'description' => 'Collect from our pickup points',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($methods as $method) {
            ShippingMethod::create($method);
        }

        // Disable free shipping by default
        $settings = ShippingSetting::first();
        if ($settings) {
            $settings->free_shipping_enabled = false;
            $settings->free_shipping_threshold = 0;
            $settings->save();
        }
    }
}
