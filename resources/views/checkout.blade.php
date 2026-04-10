<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Shirin Fashion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <h1 class="text-2xl font-bold text-gray-800">Shirin Fashion</h1>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-6">Checkout</h2>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-pink-500"></i> Delivery Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="customer_name" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input type="email" name="customer_email" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                <input type="tel" name="customer_phone" required 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Billing Address</label>
                                <input type="text" name="billing_address" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address *</label>
                                <textarea name="shipping_address" required rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-truck mr-2 text-pink-500"></i> Delivery Method
                        </h3>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="delivery_method" value="standard" checked class="w-4 h-4 text-pink-500">
                                <div class="ml-3 flex-1">
                                    <span class="font-medium">Standard Delivery</span>
                                    <p class="text-sm text-gray-500">3-5 Business Days</p>
                                </div>
                                <span class="font-semibold">{{ $shippingCost == 0 ? 'Free' : '৳ ' . $shippingCost }}</span>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="delivery_method" value="express" class="w-4 h-4 text-pink-500">
                                <div class="ml-3 flex-1">
                                    <span class="font-medium">Express Delivery</span>
                                    <p class="text-sm text-gray-500">1-2 Business Days</p>
                                </div>
                                <span class="font-semibold">৳ 250</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-credit-card mr-2 text-pink-500"></i> Payment Method
                        </h3>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="cash_on_delivery" checked class="w-4 h-4 text-pink-500">
                                <div class="ml-3">
                                    <span class="font-medium">Cash on Delivery</span>
                                    <p class="text-sm text-gray-500">Pay when you receive</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="bkash" class="w-4 h-4 text-pink-500">
                                <div class="ml-3">
                                    <span class="font-medium">bKash</span>
                                    <p class="text-sm text-gray-500">Pay via bKash</p>
                                </div>
                                <img src="https://www.bkash.com/favicon.ico" alt="bKash" class="h-6 ml-auto">
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="nagad" class="w-4 h-4 text-pink-500">
                                <div class="ml-3">
                                    <span class="font-medium">Nagad</span>
                                    <p class="text-sm text-gray-500">Pay via Nagad</p>
                                </div>
                                <img src="https://nagad.com.bd/favicon.ico" alt="Nagad" class="h-6 ml-auto">
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="card" class="w-4 h-4 text-pink-500">
                                <div class="ml-3">
                                    <span class="font-medium">Credit/Debit Card</span>
                                    <p class="text-sm text-gray-500">Pay with Visa, Mastercard</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-sticky-note mr-2 text-pink-500"></i> Order Notes (Optional)
                        </h3>
                        <textarea name="notes" rows="3" placeholder="Any special instructions for your order..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500"></textarea>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-shopping-cart mr-2 text-pink-500"></i> Order Summary
                        </h3>
                        
                        <div class="space-y-4 mb-4">
                            @foreach($carts as $cart)
                                @if($cart->product)
                                <div class="flex items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                                        @if($cart->product->image)
                                            <img src="{{ $cart->product->image }}" alt="{{ $cart->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-image text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="font-medium text-sm">{{ $cart->product->name }}</p>
                                        <p class="text-sm text-gray-500">Qty: {{ $cart->quantity }}</p>
                                    </div>
                                    <span class="font-semibold">৳ {{ number_format($cart->product->current_price * $cart->quantity, 2) }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="border-t pt-4 mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Subtotal</span>
                                <span>৳ {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Shipping</span>
                                <span>{{ $shippingCost == 0 ? 'Free' : '৳ ' . number_format($shippingCost, 2) }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Tax (5%)</span>
                                <span>৳ {{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between mb-2" id="discount-row" style="display: none;">
                                <span class="text-gray-600">Discount</span>
                                <span class="text-green-600">-৳ <span id="discount-amount">0</span></span>
                            </div>
                        </div>

                        <div class="border-t pt-4 mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apply Coupon</label>
                            <div class="flex gap-2">
                                <input type="text" id="coupon_code" name="coupon_code" placeholder="Enter coupon code"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <button type="button" onclick="applyCoupon()"
                                    class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">Apply</button>
                            </div>
                            <p id="coupon-message" class="text-sm mt-2"></p>
                        </div>

                        <div class="border-t pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span>৳ <span id="total-amount">{{ number_format($total, 2) }}</span></span>
                            </div>
                        </div>

                        <button type="submit" 
                            class="w-full mt-6 bg-pink-500 text-white py-3 rounded-lg font-semibold hover:bg-pink-600 transition">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; 2026 Shirin Fashion. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let discountAmount = 0;
        let originalTotal = {{ $total }};

        function applyCoupon() {
            const code = document.getElementById('coupon_code').value;
            const messageEl = document.getElementById('coupon-message');
            const discountRow = document.getElementById('discount-row');
            const discountEl = document.getElementById('discount-amount');
            const totalEl = document.getElementById('total-amount');

            if (!code) {
                messageEl.textContent = 'Please enter a coupon code';
                messageEl.className = 'text-sm mt-2 text-red-600';
                return;
            }

            fetch('/api/coupons/validate?code=' + code)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        discountAmount = data.discount;
                        discountEl.textContent = discountAmount.toFixed(2);
                        discountRow.style.display = 'flex';
                        totalEl.textContent = (originalTotal - discountAmount).toFixed(2);
                        messageEl.textContent = 'Coupon applied successfully!';
                        messageEl.className = 'text-sm mt-2 text-green-600';
                    } else {
                        messageEl.textContent = data.message || 'Invalid coupon code';
                        messageEl.className = 'text-sm mt-2 text-red-600';
                    }
                })
                .catch(err => {
                    messageEl.textContent = 'Error applying coupon';
                    messageEl.className = 'text-sm mt-2 text-red-600';
                });
        }

        document.querySelectorAll('input[name="delivery_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const shippingEl = document.querySelector('.bg-white.rounded-lg.shadow.p-6.sticky.top-4 .flex.justify-between.mb-2:nth-child(2) span:last-child');
                let currentShipping = parseFloat('{{ $shippingCost }}');
                
                if (this.value === 'express') {
                    currentShipping = 250;
                } else if (this.value === 'standard') {
                    currentShipping = parseFloat('{{ $subtotal >= 5000 ? 0 : 150 }}');
                }

                if (currentShipping === 0) {
                    shippingEl.textContent = 'Free';
                } else {
                    shippingEl.textContent = '৳ ' + currentShipping.toFixed(2);
                }

                let newTotal = parseFloat('{{ $subtotal }}') + currentShipping + parseFloat('{{ $tax }}') - discountAmount;
                const totalEl = document.getElementById('total-amount');
                totalEl.textContent = newTotal.toFixed(2);
                originalTotal = newTotal;
            });
        });
    </script>
</body>
</html>
