@extends('admin.layouts.master')

@section('title', 'Shipping Methods - Shirin Fashion Admin')
@section('header', 'Shipping Methods')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Shipping Methods List -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Shipping Methods</h3>
        
        @if($methods->count() > 0)
        <div class="space-y-3 mb-6">
            @foreach($methods as $method)
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
                    <form method="POST" action="{{ route('admin.settings.shipping.toggle', $method) }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 {{ $method->is_active ? 'text-yellow-600 hover:text-yellow-800' : 'text-green-600 hover:text-green-800' }}" title="{{ $method->is_active ? 'Disable' : 'Enable' }}">
                            <i class="fas fa-toggle-{{ $method->is_active ? 'on' : 'off' }}"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.settings.shipping.destroy', $method) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipping method?')">
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
        <p class="text-gray-500 mb-6">No shipping methods found. Add one below.</p>
        @endif

        <!-- Add/Edit Form -->
        <div class="border-t pt-4">
            <h4 class="font-medium text-gray-700 mb-3" id="formTitle">Add Shipping Method</h4>
            <form method="POST" action="{{ route('admin.settings.shipping.store') }}" id="shippingForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="methodName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500" placeholder="e.g., Inside City">
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Charge (৳)</label>
                    <input type="number" name="cost" id="methodCost" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500" placeholder="0.00">
                </div>
                
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                    <textarea name="description" id="methodDescription" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500" placeholder="Delivery time, etc."></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" id="methodIsActive" value="1" checked class="w-4 h-4 text-rose-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700" id="submitBtn">Add Method</button>
                    <button type="button" onclick="resetForm()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Free Shipping Settings -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Free Shipping Settings</h3>
        
        <form method="POST" action="{{ route('admin.settings.shipping.settings') }}">
            @csrf
            
            <div class="mb-4">
                <label class="flex items-center mb-3">
                    <input type="checkbox" name="free_shipping_enabled" value="1" {{ $settings->free_shipping_enabled ? 'checked' : '' }} class="w-4 h-4 text-rose-600 rounded" onchange="toggleThreshold()">
                    <span class="ml-2 text-sm text-gray-700">Enable Free Shipping</span>
                </label>
            </div>
            
            <div class="mb-4" id="thresholdContainer" style="{{ $settings->free_shipping_enabled ? '' : 'opacity: 0.5;' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Free Shipping Threshold (৳)</label>
                <input type="number" name="free_shipping_threshold" value="{{ $settings->free_shipping_threshold }}" min="0" step="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500" {{ !$settings->free_shipping_enabled ? 'disabled' : '' }}>
                <p class="text-xs text-gray-500 mt-1">Set to 0 to disable free shipping. If order total >= this value, shipping will be free.</p>
            </div>
            
            <button type="submit" class="w-full bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700">Save Settings</button>
        </form>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-700 mb-2">Current Settings</h4>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>Free Shipping: <span class="font-medium {{ $settings->free_shipping_enabled ? 'text-green-600' : 'text-red-600' }}">{{ $settings->free_shipping_enabled ? 'Enabled' : 'Disabled' }}</span></li>
                @if($settings->free_shipping_enabled)
                <li>Threshold: <span class="font-medium">৳{{ number_format($settings->free_shipping_threshold, 0) }}</span></li>
                @endif
            </ul>
        </div>
    </div>
</div>

<script>
function toggleThreshold() {
    const enabled = document.querySelector('input[name="free_shipping_enabled"]').checked;
    const container = document.getElementById('thresholdContainer');
    const input = container.querySelector('input');
    
    container.style.opacity = enabled ? '1' : '0.5';
    input.disabled = !enabled;
}

function editMethod(id, name, cost, description, isActive) {
    document.getElementById('formTitle').textContent = 'Edit Shipping Method';
    document.getElementById('methodName').value = name;
    document.getElementById('methodCost').value = cost;
    document.getElementById('methodDescription').value = description;
    document.getElementById('methodIsActive').checked = isActive;
    document.getElementById('submitBtn').textContent = 'Update Method';
    
    const form = document.getElementById('shippingForm');
    form.action = '/admin/settings/shipping/' + id;
    document.getElementById('formMethod').value = 'PUT';
}

function resetForm() {
    document.getElementById('formTitle').textContent = 'Add Shipping Method';
    document.getElementById('methodName').value = '';
    document.getElementById('methodCost').value = '';
    document.getElementById('methodDescription').value = '';
    document.getElementById('methodIsActive').checked = true;
    document.getElementById('submitBtn').textContent = 'Add Method';
    
    const form = document.getElementById('shippingForm');
    form.action = '{{ route("admin.settings.shipping.store") }}';
    document.getElementById('formMethod').value = 'POST';
}
</script>
@endsection
