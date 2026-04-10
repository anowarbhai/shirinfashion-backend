@extends('admin.layouts.master')

@section('title', 'Order #' . $order->id . ' - Shirin Fashion Admin')
@section('header', 'Order Details')

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
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Order Items</h3>
            <div class="space-y-4">
                @forelse($order->items as $item)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center space-x-4">
                        @if($item->product && $item->product->image)
                        <img src="{{ $item->product->image }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded">
                        @else
                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-box text-gray-400"></i></div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-500">{{ formatCurrencyAdmin($item->price, $currencySymbol, $currencyPosition) }} x {{ $item->quantity }}</p>
                            @if($item->attributes)
                                @php $attrs = is_string($item->attributes) ? json_decode($item->attributes, true) : $item->attributes; @endphp
                                @if(is_array($attrs) && count($attrs) > 0)
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($attrs as $attr)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $attr['name'] ?? '' }}: {{ $attr['value'] ?? '' }}</span>
                                    @endforeach
                                </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-800">{{ formatCurrencyAdmin($item->subtotal, $currencySymbol, $currencyPosition) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">No items</p>
                @endforelse
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex justify-between py-2"><span class="text-gray-500">Subtotal</span><span class="font-medium">{{ formatCurrencyAdmin($order->subtotal, $currencySymbol, $currencyPosition) }}</span></div>
                <div class="flex justify-between py-2"><span class="text-gray-500">Shipping</span><span class="font-medium">{{ formatCurrencyAdmin($order->shipping_cost, $currencySymbol, $currencyPosition) }}</span></div>
                @if($order->tax > 0)
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">
                        {{ $order->tax_name ?: 'Tax' }}
                        @if($order->tax_type && $order->tax_value)
                            ({{ $order->tax_type == 'percentage' ? $order->tax_value . '%' : formatCurrencyAdmin($order->tax_value, $currencySymbol, $currencyPosition) }})
                        @endif
                    </span>
                    <span class="font-medium">{{ formatCurrencyAdmin($order->tax, $currencySymbol, $currencyPosition) }}</span>
                </div>
                @endif
                @if($order->discount > 0)
                <div class="flex justify-between py-2 text-green-600"><span>Discount {{ $order->coupon_code ? '(' . $order->coupon_code . ')' : '' }}</span><span>-{{ formatCurrencyAdmin($order->discount, $currencySymbol, $currencyPosition) }}</span></div>
                @endif
                <div class="flex justify-between py-2 text-lg font-bold"><span>Total</span><span>{{ formatCurrencyAdmin($order->total, $currencySymbol, $currencyPosition) }}</span></div>
            </div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Order Status</h3>
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                @csrf
                @method('PUT')
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500 mb-4">
                    <option value="incomplete" {{ $order->status == 'incomplete' ? 'selected' : '' }}>⚠️ Incomplete</option>
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="w-full bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700">Update Status</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Customer Info</h3>
            <div class="space-y-2">
                <p class="text-gray-800 font-medium">{{ $order->customer_name }}</p>
                <p class="text-gray-600">{{ $order->customer_email }}</p>
                <p class="text-gray-600">{{ $order->customer_phone }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Shipping Address</h3>
            <p class="text-gray-600 whitespace-pre-line">{{ $order->shipping_address }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Payment Method</h3>
            <p class="text-gray-600 capitalize">
                @if($order->payment_method === 'cash_on_delivery')
                    Cash on Delivery
                @elseif($order->payment_method === 'online')
                    Online Payment
                @else
                    {{ $order->payment_method ?? 'N/A' }}
                @endif
            </p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="block text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Back to Orders</a>
    </div>
</div>
@endsection
