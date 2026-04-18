<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Products
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'Product'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'group' => 'Product'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'group' => 'Product'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'group' => 'Product'],
            ['name' => 'View Volume Discounts', 'slug' => 'volume-discounts.view', 'group' => 'Product'],
            ['name' => 'Edit Volume Discounts', 'slug' => 'volume-discounts.edit', 'group' => 'Product'],
            ['name' => 'View Media Library', 'slug' => 'media.view', 'group' => 'Product'],
            ['name' => 'Upload Media', 'slug' => 'media.upload', 'group' => 'Product'],
            ['name' => 'Delete Media', 'slug' => 'media.delete', 'group' => 'Product'],

            // Categories
            ['name' => 'View Categories', 'slug' => 'categories.view', 'group' => 'Category'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'group' => 'Category'],
            ['name' => 'Edit Categories', 'slug' => 'categories.edit', 'group' => 'Category'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'group' => 'Category'],

            // Orders
            ['name' => 'View Orders', 'slug' => 'orders.view', 'group' => 'Order'],
            ['name' => 'Create Orders', 'slug' => 'orders.create', 'group' => 'Order'],
            ['name' => 'Edit Orders', 'slug' => 'orders.edit', 'group' => 'Order'],
            ['name' => 'Delete Orders', 'slug' => 'orders.delete', 'group' => 'Order'],

            // Customers
            ['name' => 'View Customers', 'slug' => 'customers.view', 'group' => 'Customer'],
            ['name' => 'Edit Customers', 'slug' => 'customers.edit', 'group' => 'Customer'],
            ['name' => 'Delete Customers', 'slug' => 'customers.delete', 'group' => 'Customer'],

            // Users (for admin staff)
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'User'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'User'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'User'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'User'],

            // Roles & Permissions
            ['name' => 'View Roles', 'slug' => 'roles.view', 'group' => 'User'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'group' => 'User'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'group' => 'User'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'group' => 'User'],

            // Marketing
            ['name' => 'View Marketing', 'slug' => 'marketing.view', 'group' => 'Marketing'],
            ['name' => 'View Coupons', 'slug' => 'coupons.view', 'group' => 'Marketing'],
            ['name' => 'Create Coupons', 'slug' => 'coupons.create', 'group' => 'Marketing'],
            ['name' => 'Edit Coupons', 'slug' => 'coupons.edit', 'group' => 'Marketing'],
            ['name' => 'Delete Coupons', 'slug' => 'coupons.delete', 'group' => 'Marketing'],

            // Sliders
            ['name' => 'View Sliders', 'slug' => 'sliders.view', 'group' => 'Other'],
            ['name' => 'Create Sliders', 'slug' => 'sliders.create', 'group' => 'Other'],
            ['name' => 'Edit Sliders', 'slug' => 'sliders.edit', 'group' => 'Other'],
            ['name' => 'Delete Sliders', 'slug' => 'sliders.delete', 'group' => 'Other'],

            // Pages
            ['name' => 'View Pages', 'slug' => 'pages.view', 'group' => 'Other'],
            ['name' => 'Edit Pages', 'slug' => 'pages.edit', 'group' => 'Other'],

            // Settings
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'Settings'],
            ['name' => 'Edit Settings', 'slug' => 'settings.edit', 'group' => 'Settings'],
            ['name' => 'View General Settings', 'slug' => 'settings.general.view', 'group' => 'Settings'],
            ['name' => 'Edit General Settings', 'slug' => 'settings.general.edit', 'group' => 'Settings'],
            ['name' => 'View Fraud Checker', 'slug' => 'settings.fraud.view', 'group' => 'Settings'],
            ['name' => 'Edit Fraud Checker', 'slug' => 'settings.fraud.edit', 'group' => 'Settings'],
            ['name' => 'View SMS Integration', 'slug' => 'settings.sms.view', 'group' => 'Settings'],
            ['name' => 'Edit SMS Integration', 'slug' => 'settings.sms.edit', 'group' => 'Settings'],

            // Themes
            ['name' => 'View Themes', 'slug' => 'themes.view', 'group' => 'Settings'],
            ['name' => 'Edit Themes', 'slug' => 'themes.edit', 'group' => 'Settings'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
