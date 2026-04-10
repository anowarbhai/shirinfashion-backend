@extends('admin.layouts.master')

@section('title', $product->name . ' - Shirin Fashion Admin')
@section('header', 'Product Details')

@php
use App\Models\GeneralSetting;
$generalSettings = GeneralSetting::getSettings();
$currencySymbol = $generalSettings->currency_symbol ?? '৳';
$currencyPosition = $generalSettings->currency_position ?? 'left';

function formatCurrencyAdmin($amount, $symbol, $position) {
    $formatted = number_format($amount, 2);
    return $position === 'left' ? $symbol . $formatted : $formatted . $symbol;
}
@endphp

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center space-x-4">
            @if($product->image)
            <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
            @else
            <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-3xl"></i>
            </div>
            @endif
            <div>
                <h3 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h3>
                <p class="text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                <div class="mt-2 space-x-2">
                    @if($product->is_active)
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Active</span>
                    @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Inactive</span>
                    @endif
                    @if($product->is_featured)
                    <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded text-xs">Featured</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h4 class="font-semibold text-gray-700 mb-4">Product Information</h4>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Category</span>
                    <span class="font-medium">{{ $product->category->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Brand</span>
                    <span class="font-medium">{{ $product->brand ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
        
        <div>
            <h4 class="font-semibold text-gray-700 mb-4">Pricing & Stock</h4>
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Regular Price</span>
                    <span class="font-medium text-lg">{{ formatCurrencyAdmin($product->price, $currencySymbol, $currencyPosition) }}</span>
                    @if($product->sale_price)
                    <span class="font-medium text-lg text-green-600">{{ formatCurrencyAdmin($product->sale_price, $currencySymbol, $currencyPosition) }}</span>
                </div>
                @endif
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500">Stock Quantity</span>
                    <span class="font-medium @if($product->stock_quantity < 10) text-red-600 @endif">{{ $product->stock_quantity }}</span>
                </div>
            </div>
        </div>
    </div>
    
    @if($product->short_description)
    <div class="mt-6">
        <h4 class="font-semibold text-gray-700 mb-2">Short Description</h4>
        <p class="text-gray-600">{{ $product->short_description }}</p>
    </div>
    @endif
    
    @if($product->description)
    <div class="mt-6">
        <h4 class="font-semibold text-gray-700 mb-2">Description</h4>
        <p class="text-gray-600">{{ $product->description }}</p>
    </div>
    @endif
</div>
@endsection
