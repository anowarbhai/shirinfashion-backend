@extends('admin.layouts.master')

@section('title', 'Dashboard - Shirin Fashion Admin')
@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-4 md:mb-8">
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">Products</p>
                <p class="text-xl md:text-3xl font-bold text-gray-800">{{ $stats['total_products'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-14 md:h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box text-blue-500 text-lg md:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">Orders</p>
                <p class="text-xl md:text-3xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-14 md:h-14 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-bag text-green-500 text-lg md:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">Customers</p>
                <p class="text-xl md:text-3xl font-bold text-gray-800">{{ $stats['total_customers'] }}</p>
            </div>
            <div class="w-10 h-10 md:w-14 md:h-14 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-purple-500 text-lg md:text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 border-l-4 border-rose-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs md:text-sm">Revenue</p>
                <p class="text-xl md:text-3xl font-bold text-gray-800">{{ $currencySymbol }}{{ number_format($stats['total_revenue'], 2) }}</p>
            </div>
            <div class="w-10 h-10 md:w-14 md:h-14 bg-rose-100 rounded-full flex items-center justify-center">
                <i class="fas fa-dollar-sign text-rose-500 text-lg md:text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-700">Pending Orders</h3>
            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">{{ $stats['pending_orders'] }}</span>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-700">Categories</h3>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">{{ $stats['total_categories'] }}</span>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-700">Low Stock</h3>
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">{{ $stats['low_stock_products'] }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700">Recent Orders</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recent_orders as $order)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-gray-500"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-500">{{ $order->user->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-800">{{ $currencySymbol }}{{ number_format($order->total, 2) }}</p>
                        <span class="px-2 py-1 rounded text-xs @if($order->status === 'pending') bg-yellow-100 text-yellow-700 @elseif($order->status === 'completed') bg-green-100 text-green-700 @elseif($order->status === 'processing') bg-blue-100 text-blue-700 @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">No orders yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700">Top Products</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($top_products as $index => $product)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-medium text-gray-800">{{ $product->order_items_count }} sales</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-4">No products yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
