@extends('admin.layouts.master')

@section('title', 'Products - Shirin Fashion Admin')
@section('header', 'Products')

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
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center flex-wrap gap-3">
        <form method="GET" class="relative flex-1 md:flex-none">
            <input type="text" name="search" placeholder="Search products..." 
                value="{{ request('search') }}"
                class="w-full md:w-64 px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            <button type="submit" class="absolute right-0 top-0 h-full bg-rose-600 text-white px-4 rounded-r-lg hover:bg-rose-700 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-medium flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-download"></i>Export
            </a>
            <button type="button" onclick="document.getElementById('importModal').showModal()" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition font-medium flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-upload"></i>Import
            </button>
            <a href="{{ route('admin.products.create') }}" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700 transition font-medium flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i>Add
            </a>
        </div>
    </div>
    
    <!-- Import Modal -->
    <dialog id="importModal" class="modal p-6 rounded-lg shadow-xl border border-gray-200">
        <div class="w-full max-w-md">
            <h3 class="text-xl font-semibold mb-4">Import Products from CSV</h3>
            <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select CSV File</label>
                    <input type="file" name="csv_file" accept=".csv" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('importModal').close()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Import</button>
                </div>
            </form>
        </div>
    </dialog>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        @if($product->image)
                        <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset($product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded">
                        @else
                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $product->name }}</p>
                        <p class="text-sm text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $product->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ formatCurrencyAdmin($product->price, $currencySymbol, $currencyPosition) }}</p>
                        @if($product->sale_price)
                        <p class="text-sm text-green-600">{{ formatCurrencyAdmin($product->sale_price, $currencySymbol, $currencyPosition) }} sale</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="@if($product->stock_quantity < 10) text-red-600 @else text-gray-600 @endif">
                            {{ $product->stock_quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Active</span>
                        @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.products.show', $product) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">No products found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($products as $product)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Product Image and Name -->
            <div class="flex gap-3 mb-3">
                <div class="flex-shrink-0">
                    @if($product->image)
                    <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset($product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg">
                    @else
                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 line-clamp-2">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1 truncate">Category: {{ $product->category->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 truncate">SKU: {{ $product->sku ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Price -->
            <div class="flex items-center justify-between mb-3">
                @if($product->sale_price)
                <span class="text-sm font-semibold text-gray-700">Regular: <span class="text-gray-600">{{ formatCurrencyAdmin($product->price, $currencySymbol, $currencyPosition) }}</span></span>
                <span class="text-sm font-semibold text-green-600">Sale: {{ formatCurrencyAdmin($product->sale_price, $currencySymbol, $currencyPosition) }}</span>
                @else
                <span class="text-lg font-bold text-gray-800">{{ formatCurrencyAdmin($product->price, $currencySymbol, $currencyPosition) }}</span>
                @endif
            </div>

            <!-- Stock Status -->
            <div class="flex items-center justify-between text-sm mb-3 pb-3 border-b border-gray-100">
                <span class="text-gray-600">Stock Quantity</span>
                <span class="font-medium @if($product->stock_quantity < 10) text-red-600 @else text-gray-800 @endif">
                    {{ $product->stock_quantity }} @if($product->stock_quantity < 10) <i class="fas fa-exclamation-circle"></i> @endif
                </span>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <span class="px-2 py-1 text-xs font-semibold rounded-full @if($product->is_active) bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                    @if($product->is_active) Active @else Inactive @endif
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-blue-50">
                        <i class="fas fa-arrow-right"></i>
                        View
                    </a>
                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-yellow-50">
                        <i class="fas fa-edit"></i>
                        Edit
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">No products found</div>
        @endforelse
    </div>
    
    @if($products->hasPages())
    <div class="p-6 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
