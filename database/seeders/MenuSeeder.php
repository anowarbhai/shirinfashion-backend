<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Footer Column 1 - Quick Links
        $footer1 = Menu::updateOrCreate(
            ['slug' => 'footer-1'],
            ['name' => 'Footer Column 1 - Quick Links', 'location' => 'footer1', 'is_active' => true]
        );

        $footer1Items = [
            ['title' => 'Shop', 'url' => '/shop', 'order' => 1],
            ['title' => 'Categories', 'url' => '/categories', 'order' => 2],
            ['title' => 'About Us', 'url' => '/about', 'order' => 3],
            ['title' => 'Contact', 'url' => '/contact', 'order' => 4],
        ];

        foreach ($footer1Items as $index => $item) {
            MenuItem::updateOrCreate(
                ['menu_id' => $footer1->id, 'title' => $item['title']],
                ['title' => $item['title'], 'url' => $item['url'], 'order' => $item['order'], 'is_active' => true]
            );
        }

        // Footer Column 2 - Customer Service
        $footer2 = Menu::updateOrCreate(
            ['slug' => 'footer-2'],
            ['name' => 'Footer Column 2 - Customer Service', 'location' => 'footer2', 'is_active' => true]
        );

        $footer2Items = [
            ['title' => 'FAQ', 'url' => '/faq', 'order' => 1],
            ['title' => 'Shipping Info', 'url' => '/shipping', 'order' => 2],
            ['title' => 'Returns', 'url' => '/returns', 'order' => 3],
            ['title' => 'Privacy Policy', 'url' => '/privacy-policy', 'order' => 4],
            ['title' => 'Terms of Service', 'url' => '/terms', 'order' => 5],
        ];

        foreach ($footer2Items as $index => $item) {
            MenuItem::updateOrCreate(
                ['menu_id' => $footer2->id, 'title' => $item['title']],
                ['title' => $item['title'], 'url' => $item['url'], 'order' => $item['order'], 'is_active' => true]
            );
        }

        // Header Menu
        $header = Menu::updateOrCreate(
            ['slug' => 'header-main'],
            ['name' => 'Main Navigation', 'location' => 'header', 'is_active' => true]
        );

        $headerItems = [
            ['title' => 'Home', 'url' => '/', 'order' => 1],
            ['title' => 'Shop', 'url' => '/shop', 'order' => 2],
            ['title' => 'Categories', 'url' => '/categories', 'order' => 3],
            ['title' => 'About', 'url' => '/about', 'order' => 4],
            ['title' => 'Contact', 'url' => '/contact', 'order' => 5],
        ];

        foreach ($headerItems as $index => $item) {
            MenuItem::updateOrCreate(
                ['menu_id' => $header->id, 'title' => $item['title']],
                ['title' => $item['title'], 'url' => $item['url'], 'order' => $item['order'], 'is_active' => true]
            );
        }

        // Mobile Menu
        $mobile = Menu::updateOrCreate(
            ['slug' => 'mobile-menu'],
            ['name' => 'Mobile Menu', 'location' => 'mobile', 'is_active' => true]
        );

        $mobileItems = [
            ['title' => 'Home', 'url' => '/', 'order' => 1],
            ['title' => 'Shop', 'url' => '/shop', 'order' => 2],
            ['title' => 'Categories', 'url' => '/categories', 'order' => 3],
            ['title' => 'Cart', 'url' => '/cart', 'order' => 4],
            ['title' => 'Account', 'url' => '/account', 'order' => 5],
            ['title' => 'About', 'url' => '/about', 'order' => 6],
            ['title' => 'Contact', 'url' => '/contact', 'order' => 7],
        ];

        foreach ($mobileItems as $index => $item) {
            MenuItem::updateOrCreate(
                ['menu_id' => $mobile->id, 'title' => $item['title']],
                ['title' => $item['title'], 'url' => $item['url'], 'order' => $item['order'], 'is_active' => true]
            );
        }

        $this->command->info('Menus seeded successfully!');
    }
}
