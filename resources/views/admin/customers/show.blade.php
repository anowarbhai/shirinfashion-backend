@extends('admin.layouts.master')

@section('title', 'Customer #' . $customer->id . ' - Shirin Fashion Admin')
@section('header', 'Customer Details')

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
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Customer Info -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 font-bold text-2xl mx-auto mb-3">
                    {{ substr($customer->name, 0, 1) }}
                </div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $customer->name }}</h2>
                <p class="text-sm text-gray-500">
                    @if($customer->is_admin)
                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs">Administrator</span>
                    @else
                    <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs">Customer</span>
                    @endif
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Email</p>
                    <p class="font-medium text-gray-900">{{ $customer->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Phone</p>
                    <p class="font-medium text-gray-900">{{ $customer->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Registered</p>
                    <p class="font-medium text-gray-900">{{ $customer->created_at->format('F d, Y') }}</p>
                    <p class="text-sm text-gray-500">{{ $customer->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.customers.index') }}" class="block text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            ← Back to Customers
        </a>
    </div>

    <!-- Orders -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Order History ({{ $customer->orders->count() }})</h3>
            
            @if($customer->orders->count() > 0)
            <div class="space-y-4">
                @foreach($customer->orders as $order)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-medium text-gray-900">Order #{{ $order->id }}</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs rounded 
                                @if($order->status == 'pending') bg-yellow-100 text-yellow-700
                                @elseif($order->status == 'processing') bg-blue-100 text-blue-700
                                @elseif($order->status == 'shipped') bg-purple-100 text-purple-700
                                @elseif($order->status == 'delivered') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            <p class="text-lg font-bold text-rose-600 mt-1">{{ formatCurrencyAdmin($order->total, $currencySymbol, $currencyPosition) }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>{{ $order->items->count() }} item(s)</p>
                    </div>
                    <div class="mt-3 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            View Order →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-shopping-bag text-4xl mb-3 text-gray-300"></i>
                <p>No orders found for this customer.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
