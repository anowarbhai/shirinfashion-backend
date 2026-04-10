<?php

namespace Database\Seeders;

use App\Models\ThemeSetting;
use Illuminate\Database\Seeder;

class ThemeSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = ThemeSetting::first();
        
        if (!$settings) {
            ThemeSetting::create([
                'company_name' => 'Shirin Fashion',
                'tagline' => 'Premium Fashion Store',
                'company_details' => 'Your trusted destination for premium fashion and cosmetics. We offer the best quality products at affordable prices.',
                'email' => 'contact@shirinfashion.com',
                'phone' => '+880 1234-567890',
                'address' => '123 Fashion Street, Dhaka, Bangladesh',
                'facebook' => 'https://facebook.com/shirinfashion',
                'instagram' => 'https://instagram.com/shirinfashion',
                'twitter' => 'https://twitter.com/shirinfashion',
                'header_style' => 'style1',
                'footer_style' => 'style1',
                'primary_menu' => 'main',
            ]);
        }
    }
}
