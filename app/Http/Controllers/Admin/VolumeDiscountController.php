<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class VolumeDiscountController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->select('id', 'name', 'image', 'price', 'sale_price')
            ->get();

        return view('admin.volume-discounts.index', compact('products'));
    }
}
