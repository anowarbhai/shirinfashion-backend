<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Skincare', 'slug' => 'skincare', 'description' => 'Premium skincare products for radiant skin', 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Makeup', 'slug' => 'makeup', 'description' => 'Professional makeup collection', 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Fragrance', 'slug' => 'fragrance', 'description' => 'Luxury perfumes and scents', 'is_active' => true, 'sort_order' => 3],
            ['name' => 'Haircare', 'slug' => 'haircare', 'description' => 'Premium hair care products', 'is_active' => true, 'sort_order' => 4],
            ['name' => 'Bath & Body', 'slug' => 'bath-body', 'description' => 'Relaxing bath and body essentials', 'is_active' => true, 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
