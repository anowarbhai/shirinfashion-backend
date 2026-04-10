@extends('admin.layouts.master')

@section('title', 'Create Order')

@section('header', 'Create Order')

@php
use App\Models\GeneralSetting;
$generalSettings = GeneralSetting::getSettings();
$currencySymbol = $generalSettings->currency_symbol ?? '৳';
$currencyPosition = $generalSettings->currency_position ?? 'left';
@endphp

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<div class="max-w-6xl mx-auto">
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Customer Information</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name *</label>
                        <input type="text" name="customer_name" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="text" name="customer_phone" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            placeholder="e.g., 017XXXXXXXX">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="customer_email" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Link to Existing User</label>
                        <select name="user_id" id="user-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                            <option value="">-- Select Customer (Optional) --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone ?? 'No phone' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Shipping & Payment -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Shipping & Payment</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Address *</label>
                        <textarea name="shipping_address" rows="3" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            placeholder="Full shipping address"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Billing Address</label>
                        <textarea name="billing_address" rows="2" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            placeholder="Same as shipping"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method *</label>
                        <select name="delivery_method" id="delivery_method" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                            <option value="pickup">Pickup from Store</option>
                            <option value="home_delivery">Home Delivery</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                        <select name="payment_method" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                            <option value="cash">Cash on Delivery</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="card">Card Payment</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Order Products</h3>
                
                <div id="product-rows">
                    <div class="product-row flex gap-4 mb-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                            <select name="products[0][product_id]" id="product-0" required 
                                class="product-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">
                                        {{ $product->name }} - {{ $product->sku }} (Stock: {{ $product->stock_quantity }}) - {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-24">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                            <input type="number" name="products[0][quantity]" value="1" min="1" required 
                                class="quantity w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                        </div>
                        <div class="w-32">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                            <div class="subtotal-display px-4 py-2 bg-gray-100 rounded-lg text-gray-600">{{ $currencySymbol }}0.00</div>
                        </div>
                        <button type="button" class="remove-product px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <button type="button" id="add-product" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>

                <div class="mt-6 flex justify-end">
                    <div class="w-64 space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span id="order-subtotal">{{ $currencySymbol }}0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping:</span>
                            <span id="order-shipping">{{ $currencySymbol }}0.00</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-gray-800 border-t pt-2">
                            <span>Total:</span>
                            <span id="order-total">{{ $currencySymbol }}0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                <textarea name="notes" rows="2" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                    placeholder="Any special instructions"></textarea>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg hover:bg-rose-600 transition">
                    Create Order
                </button>
                <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

const currencySymbol = '{{ $currencySymbol }}';
const productPrices = {};
@foreach($products as $product)
productPrices[{{ $product->id }}] = {{ $product->price }};
@endforeach

// Store users data
const usersData = [
@foreach($customers as $customer)
    { id: {{ $customer->id }}, name: "{{ $customer->name }}", phone: "{{ $customer->phone ?? '' }}", address: "{{ $customer->address ?? '' }}" },
@endforeach
];

$(document).ready(function() {
    // Initialize Select2 for user select
    $('#user-select').select2();
    
    // Handle user selection - auto fill customer details
    $('#user-select').on('change select2:select', function() {
        const userId = $(this).val();
        
        if (userId) {
            const user = usersData.find(u => u.id == userId);
            if (user) {
                $('input[name="customer_name"]').val(user.name);
                $('input[name="customer_phone"]').val(user.phone);
                $('textarea[name="shipping_address"]').val(user.address);
            }
        }
    });
    
    // Initialize Select2 for first product
    $('#product-0').select2({
        placeholder: 'Search Product...',
        allowClear: true
    });
    
    calculateTotal();
});

let productCount = 1;

$('#add-product').on('click', function() {
    const container = $('#product-rows');
    const newRow = $(`
        <div class="product-row flex gap-4 mb-4 items-end">
            <div class="flex-1">
                <select name="products[${productCount}][product_id]" id="product-${productCount}" required 
                    class="product-select w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock_quantity }}">
                            {{ $product->name }} - {{ $product->sku }} (Stock: {{ $product->stock_quantity }}) - {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-24">
                <input type="number" name="products[${productCount}][quantity]" value="1" min="1" required 
                    class="quantity w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
            </div>
            <div class="w-32">
                <div class="subtotal-display px-4 py-2 bg-gray-100 rounded-lg text-gray-600">{{ $currencySymbol }}0.00</div>
            </div>
            <button type="button" class="remove-product px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `);
    
    container.append(newRow);
    
    // Initialize Select2 for new product select
    $(`#product-${productCount}`).select2({
        placeholder: 'Search Product...',
        allowClear: true
    });
    
    productCount++;
    
    attachProductEvents(newRow);
    updateRemoveButtons();
});

function attachProductEvents(row) {
    const select = row.find('.product-select');
    const quantity = row.find('.quantity');
    const subtotalDisplay = row.find('.subtotal-display');

    // Handle native select change
    select.on('change', function() {
        updateRowSubtotal(row);
        calculateTotal();
    });

    // Handle Select2 selection
    select.on('select2:select', function() {
        updateRowSubtotal(row);
        calculateTotal();
    });

    quantity.on('input', function() {
        updateRowSubtotal(row);
        calculateTotal();
    });

    row.find('.remove-product').on('click', function() {
        row.remove();
        calculateTotal();
        updateRemoveButtons();
    });
}

function updateRowSubtotal(row) {
    const select = row.find('.product-select');
    const quantity = row.find('.quantity');
    const subtotalDisplay = row.find('.subtotal-display');
    
    const productId = select.val();
    const price = productPrices[productId] || 0;
    const qty = parseInt(quantity.val()) || 0;
    
    subtotalDisplay.text(currencySymbol + (price * qty).toFixed(2));
}

function calculateTotal() {
    let subtotal = 0;
    $('.product-row').each(function() {
        const row = $(this);
        const select = row.find('.product-select');
        const quantity = row.find('.quantity');
        const productId = select.val();
        const price = productPrices[productId] || 0;
        const qty = parseInt(quantity.val()) || 0;
        subtotal += price * qty;
    });

    const deliveryMethod = $('#delivery_method').val();
    const shipping = deliveryMethod === 'home_delivery' ? 100 : 0;
    const total = subtotal + shipping;

    $('#order-subtotal').text(currencySymbol + subtotal.toFixed(2));
    $('#order-shipping').text(currencySymbol + shipping.toFixed(2));
    $('#order-total').text(currencySymbol + total.toFixed(2));
}

function updateRemoveButtons() {
    const rows = $('.product-row');
    if (rows.length > 1) {
        rows.find('.remove-product').show();
    } else {
        rows.find('.remove-product').hide();
    }
}

$('#delivery_method').on('change', calculateTotal);

// Initial setup
$('.product-row').each(function() {
    attachProductEvents($(this));
});
</script>
@endsection
