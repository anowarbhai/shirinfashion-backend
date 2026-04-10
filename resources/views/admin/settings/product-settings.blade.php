@extends('admin.layouts.master')

@section('header', 'Product Settings')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<style>
.tab-active {
    border-bottom: 2px solid #e11d48;
    color: #e11d48;
}
.tab-inactive {
    color: #6b7280;
}
.tab-inactive:hover {
    color: #374151;
}
@media (max-width: 768px) {
    #tab-tax { display: flex !important; }
    .tab-active { display: flex !important; }
}
</style>

<div class="bg-white rounded-lg shadow">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 overflow-x-auto">
        <nav class="flex -mb-px min-w-full" aria-label="Tabs">
            <button type="button" onclick="switchTab('reviews')" id="tab-reviews" class="tab-inactive py-4 px-3 md:px-6 text-sm font-medium whitespace-nowrap">
                <i class="fas fa-star mr-2"></i><span class="hidden md:inline">Review Settings</span>
            </button>
            <button type="button" onclick="switchTab('shipping')" id="tab-shipping" class="tab-inactive py-4 px-3 md:px-6 text-sm font-medium whitespace-nowrap">
                <i class="fas fa-shipping-fast mr-2"></i><span class="hidden md:inline">Shipping</span>
            </button>
            <button type="button" onclick="switchTab('payment')" id="tab-payment" class="tab-inactive py-4 px-3 md:px-6 text-sm font-medium whitespace-nowrap">
                <i class="fas fa-credit-card mr-2"></i><span class="hidden md:inline">Payment Methods</span>
            </button>
            <button type="button" onclick="switchTab('tax')" id="tab-tax" class="tab-active py-4 px-4 md:px-6 text-sm font-medium whitespace-nowrap">
                <i class="fas fa-percent mr-2"></i><span class="hidden md:inline">Tax</span>
            </button>
            <button type="button" onclick="switchTab('contact')" id="tab-contact" class="tab-inactive py-4 px-3 md:px-6 text-sm font-medium whitespace-nowrap">
                <i class="fas fa-phone mr-2"></i><span class="hidden md:inline">Contact</span>
            </button>
        </nav>
    </div>

    <!-- Tab Contents -->
    <div class="p-4 md:p-6">
        
        <!-- Tab 1: Review Settings -->
        <div id="content-reviews">
            <form method="POST" action="{{ route('admin.settings.product.update') }}">
                @csrf
                <h3 class="text-lg font-medium text-gray-900 mb-4">Review Settings</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="global_reviews_enabled" value="1" {{ old('global_reviews_enabled', $reviewSettings['global_reviews_enabled']) ? 'checked' : '' }}
                            class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700">Enable Reviews System</label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">When disabled, customers cannot submit reviews on any product.</p>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="global_avg_rating_enabled" value="1" {{ old('global_avg_rating_enabled', $reviewSettings['global_avg_rating_enabled']) ? 'checked' : '' }}
                            class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700">Show Average Rating</label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">When disabled, average ratings will not be shown on product cards and details pages.</p>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="guest_reviews_enabled" value="1" {{ old('guest_reviews_enabled', $reviewSettings['guest_reviews_enabled']) ? 'checked' : '' }}
                            class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                        <label class="ml-2 text-sm text-gray-700">Allow Guest Reviews</label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">When enabled, guests can submit reviews without logging in. Guest reviews require admin approval.</p>
                </div>
                <div class="mt-6 pt-6 border-t">
                    <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
                        Save Review Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab 5: Tax (Show by default on mobile) -->
        <div id="content-shipping">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <!-- Shipping Methods List -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Methods</h3>
                    
                    @if($shippingMethods->count() > 0)
                    <div class="space-y-3 mb-6">
                        @foreach($shippingMethods as $method)
                        <div class="flex items-center justify-between p-4 border rounded-lg {{ $method->is_active ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-medium text-gray-800">{{ $method->name }}</h4>
                                    @if(!$method->is_active)
                                    <span class="px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded">Inactive</span>
                                    @endif
                                </div>
                                @if($method->description)
                                <p class="text-sm text-gray-500">{{ $method->description }}</p>
                                @endif
                                <p class="text-sm font-medium text-rose-600">৳{{ number_format($method->cost, 2) }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="editMethod({{ $method->id }}, '{{ $method->name }}', {{ $method->cost }}, '{{ $method->description ?? '' }}', {{ $method->is_active ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.settings.product.shipping.toggle', $method) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 {{ $method->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}">
                                        <i class="fas fa-toggle-{{ $method->is_active ? 'on' : 'off' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.settings.product.shipping.destroy', $method) }}" class="inline" onsubmit="return confirm('Delete this shipping method?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 mb-6">No shipping methods found.</p>
                    @endif

                    <!-- Add/Edit Form -->
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-700 mb-3" id="shippingFormTitle">Add Shipping Method</h4>
                        <form method="POST" action="{{ route('admin.settings.product.shipping.store') }}" id="shippingForm">
                            @csrf
                            <input type="hidden" name="_method" id="shippingFormMethod" value="POST">
                            
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" id="methodName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                            </div>
                            
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cost (৳)</label>
                                <input type="number" name="cost" id="methodCost" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                            </div>
                            
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" id="methodDescription" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" id="methodIsActive" value="1" checked class="w-4 h-4 text-rose-600 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                            
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700" id="shippingSubmitBtn">Add Method</button>
                                <button type="button" onclick="resetShippingForm()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Free Shipping Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Free Shipping Settings</h3>
                    
                    <form method="POST" action="{{ route('admin.settings.product.shipping-settings.update') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="flex items-center mb-3">
                                <input type="checkbox" name="free_shipping_enabled" value="1" {{ $shippingSettings->free_shipping_enabled ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded">
                                <span class="ml-2 text-sm text-gray-700">Enable Free Shipping</span>
                            </label>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Free Shipping Threshold (৳)</label>
                            <input type="number" name="free_shipping_threshold" value="{{ $shippingSettings->free_shipping_threshold }}" min="0" step="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                            <p class="text-xs text-gray-500 mt-1">Set to 0 to disable. If order >= this, shipping is free.</p>
                        </div>
                        
                        <button type="submit" class="w-full bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700">Save Shipping Settings</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab 3: Payment Methods -->
        <div id="content-payment" class="hidden">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Methods</h3>
            
            <div class="space-y-4">
                @foreach($paymentMethods as $method)
                <div class="flex items-center justify-between p-4 border rounded-lg {{ $method['is_active'] ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas {{ $method['id'] == 1 ? 'fa-money-bill-wave' : 'fa-credit-card' }} text-gray-600"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">{{ $method['name'] }}</h4>
                            <span class="px-2 py-0.5 text-xs {{ $method['is_active'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }} rounded">
                                {{ $method['is_active'] ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <button class="text-rose-600 hover:text-rose-800">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Additional payment methods (Online Payment, Bkash, Nagad, etc.) can be integrated upon request. Contact development team.
                </p>
            </div>
        </div>

        <!-- Tab 4: Tax Settings -->
        <div id="content-tax" class="hidden">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tax Configuration</h3>
            
            <form method="POST" action="{{ route('admin.settings.product.tax-settings.update') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="tax_enabled" value="1" {{ $taxSettings->tax_enabled ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded">
                                <span class="ml-2 text-sm text-gray-700">Enable Tax</span>
                            </label>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Name</label>
                            <input type="text" name="tax_name" value="{{ $taxSettings->tax_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                            <p class="text-xs text-gray-500 mt-1">Example: VAT, Sales Tax, GST</p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Type</label>
                            <select name="tax_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                                <option value="percentage" {{ $taxSettings->tax_type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ $taxSettings->tax_type == 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Value</label>
                            <input type="number" name="tax_value" value="{{ $taxSettings->tax_value }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                            <p class="text-xs text-gray-500 mt-1">
                                @if($taxSettings->tax_type == 'percentage')
                                Current: {{ $taxSettings->tax_value }}%
                                @else
                                Current: ৳{{ $taxSettings->tax_value }}
                                @endif
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Price Type</label>
                            <select name="tax_price_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                                <option value="exclusive" {{ ($taxSettings->tax_price_type ?? 'exclusive') == 'exclusive' ? 'selected' : '' }}>Exclusive (Tax added on top)</option>
                                <option value="inclusive" {{ $taxSettings->tax_price_type == 'inclusive' ? 'selected' : '' }}>Inclusive (Tax included in price)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Exclusive: ৳100 + 5% VAT = ৳105<br>
                                Inclusive: ৳100 (includes 5% VAT)
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Tax Preview -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-700 mb-3">Tax Preview</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal: ৳1,000</span>
                        </div>
                        @if(($taxSettings->tax_price_type ?? 'exclusive') == 'inclusive')
                        <div class="flex justify-between text-green-600">
                            <span>{{ $taxSettings->tax_name }} Included ({{ $taxSettings->tax_value }}%):</span>
                            <span class="font-medium">৳{{ number_format($taxSettings->calculateTax(1000), 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ $taxSettings->tax_name }} ({{ $taxSettings->tax_type == 'percentage' ? $taxSettings->tax_value . '%' : '৳' . $taxSettings->tax_value }}):</span>
                            <span class="font-medium">৳{{ number_format(($taxSettings->tax_price_type ?? 'exclusive') == 'inclusive' ? 0 : $taxSettings->calculateTax(1000), 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold border-t pt-2">
                            <span>Total with Tax:</span>
                            <span class="text-rose-600">৳{{ number_format(($taxSettings->tax_price_type ?? 'exclusive') == 'inclusive' ? 1000 : 1000 + $taxSettings->calculateTax(1000), 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
                        Save Tax Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab 5: Contact Settings -->
        <div id="content-contact" class="hidden">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Product Page Contact Settings</h3>
            <p class="text-sm text-gray-600 mb-6">Add WhatsApp and Call buttons on product pages for customer inquiries.</p>
            
            <form method="POST" action="{{ route('admin.settings.product.contact.update') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_buttons_enabled" value="1" {{ $contactSettings['contact_buttons_enabled'] ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded">
                                <span class="ml-2 text-sm text-gray-700">Show Contact Buttons on Product Page</span>
                            </label>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fab fa-whatsapp text-green-500 mr-1"></i>WhatsApp Number
                            </label>
                            <input type="text" name="whatsapp_number" value="{{ $contactSettings['whatsapp_number'] ?? '' }}" placeholder="e.g., +8801712345678" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                            <p class="text-xs text-gray-500 mt-1">Enter number with country code (e.g., +8801712345678)</p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-phone text-blue-500 mr-1"></i>Call Number
                            </label>
                            <input type="text" name="call_number" value="{{ $contactSettings['call_number'] ?? '' }}" placeholder="e.g., +8801712345678" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                            <p class="text-xs text-gray-500 mt-1">Phone number for direct calls</p>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Message Template</label>
                            <textarea name="whatsapp_message" rows="3" placeholder="Hi, I'm interested in this product: {product_name}. Please provide more details." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">{{ $contactSettings['whatsapp_message'] ?? '' }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Use {product_name} to auto-fill product name</p>
                        </div>
                    </div>
                </div>
                
                <!-- Preview -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-700 mb-3">Button Preview</h4>
                    <div class="flex gap-3">
                        @if($contactSettings['whatsapp_number'])
                        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp
                        </a>
                        @endif
                        @if($contactSettings['call_number'])
                        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg">
                            <i class="fas fa-phone"></i>
                            Call Now
                        </a>
                        @endif
                        @if(!$contactSettings['whatsapp_number'] && !$contactSettings['call_number'])
                        <p class="text-sm text-gray-500">Save settings to see preview</p>
                        @endif
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
                        Save Contact Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all contents
    document.getElementById('content-reviews').classList.add('hidden');
    document.getElementById('content-shipping').classList.add('hidden');
    document.getElementById('content-payment').classList.add('hidden');
    document.getElementById('content-tax').classList.add('hidden');
    document.getElementById('content-contact').classList.add('hidden');
    
    // Remove active class from all tabs
    document.getElementById('tab-reviews').classList.remove('tab-active');
    document.getElementById('tab-reviews').classList.add('tab-inactive');
    document.getElementById('tab-shipping').classList.remove('tab-active');
    document.getElementById('tab-shipping').classList.add('tab-inactive');
    document.getElementById('tab-payment').classList.remove('tab-active');
    document.getElementById('tab-payment').classList.add('tab-inactive');
    document.getElementById('tab-tax').classList.remove('tab-active');
    document.getElementById('tab-tax').classList.add('tab-inactive');
    document.getElementById('tab-contact').classList.remove('tab-active');
    document.getElementById('tab-contact').classList.add('tab-inactive');
    
    // Show selected content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab
    document.getElementById('tab-' + tabName).classList.add('tab-active');
    document.getElementById('tab-' + tabName).classList.remove('tab-inactive');
}

function editMethod(id, name, cost, description, isActive) {
    document.getElementById('shippingFormTitle').textContent = 'Edit Shipping Method';
    document.getElementById('methodName').value = name;
    document.getElementById('methodCost').value = cost;
    document.getElementById('methodDescription').value = description;
    document.getElementById('methodIsActive').checked = isActive;
    document.getElementById('shippingSubmitBtn').textContent = 'Update Method';
    
    const form = document.getElementById('shippingForm');
    form.action = '/admin/settings/product/shipping/' + id;
    document.getElementById('shippingFormMethod').value = 'PUT';
}

function resetShippingForm() {
    document.getElementById('shippingFormTitle').textContent = 'Add Shipping Method';
    document.getElementById('methodName').value = '';
    document.getElementById('methodCost').value = '';
    document.getElementById('methodDescription').value = '';
    document.getElementById('methodIsActive').checked = true;
    document.getElementById('shippingSubmitBtn').textContent = 'Add Method';
    
    const form = document.getElementById('shippingForm');
    form.action = '{{ route("admin.settings.product.shipping.store") }}';
    document.getElementById('shippingFormMethod').value = 'POST';
}
</script>
@endsection
