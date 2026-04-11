@extends('admin.layouts.master')

@section('title', 'Volume Discounts')

@section('content')
<div>
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Volume Discounts</h1>
                <p class="text-sm text-gray-500 mt-1">Manage quantity-based pricing for products</p>
            </div>
        </div>

        <div class="flex h-[calc(100vh-200px)]">
            <!-- Left Panel - Product List -->
            <div class="w-1/3 bg-white border border-gray-200 rounded-lg flex flex-col mr-4">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Select Product</h3>
                    <input type="text" id="productSearch" placeholder="Search products..." 
                        class="mt-2 w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500">
                </div>
                <div class="flex-1 overflow-y-auto" id="productList">
                    @foreach($products as $product)
                    <div class="p-3 border-b border-gray-100 cursor-pointer hover:bg-gray-50 product-item" 
                        data-product-id="{{ $product->id }}" onclick="selectProduct({{ $product->id }}, '{{ addslashes($product->name) }}')">
                        <div class="flex items-center gap-3">
                            @if($product->image)
                            <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset($product->image) }}" class="w-10 h-10 object-cover rounded">
                            @endif
                            <div>
                                <p class="font-medium text-gray-800 text-sm">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">৳{{ $product->sale_price ?? $product->price }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Panel - Volume Discounts -->
            <div class="flex-1 flex flex-col bg-white border border-gray-200 rounded-lg">
                <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-800">Volume Discounts</h3>
                            <p id="selectedProductName" class="text-sm text-gray-500">Select a product from the left</p>
                        </div>
                        <button type="button" onclick="addTier()" id="addTierBtn" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            + Add Tier
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4" id="tiersContainer">
                    <p class="text-center text-gray-500 py-10">Select a product to manage volume discounts</p>
                </div>

                <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg" id="saveSection" style="display: none;">
                    <button type="button" onclick="saveTiers()" class="w-full px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                        Save Volume Discounts
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedProductId = null;
let tiers = [];

const productSearch = document.getElementById('productSearch');
const productList = document.getElementById('productList');
const tiersContainer = document.getElementById('tiersContainer');
const selectedProductName = document.getElementById('selectedProductName');
const addTierBtn = document.getElementById('addTierBtn');
const saveSection = document.getElementById('saveSection');

productSearch.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(query) ? 'block' : 'none';
    });
});

function selectProduct(id, name) {
    selectedProductId = id;
    selectedProductName.textContent = name;
    addTierBtn.disabled = false;
    loadTiers(id);
}

function loadTiers(productId) {
    fetch(`/api/volume-discounts?product_id=${productId}`)
        .then(res => res.json())
        .then(data => {
            tiers = data.data || [];
            renderTiers();
            saveSection.style.display = 'block';
        });
}

function renderTiers() {
    if (tiers.length === 0) {
        tiersContainer.innerHTML = '<p class="text-center text-gray-500 py-10">No volume discounts. Click "Add Tier" to create one.</p>';
        return;
    }

    let html = '';
    tiers.forEach((tier, index) => {
        html += `
        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
            <div class="flex justify-between items-start mb-4">
                <h4 class="font-semibold text-gray-800">Tier ${index + 1}</h4>
                <button type="button" onclick="removeTier(${index})" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${tier.quantity}" 
                        onchange="updateTier(${index}, 'quantity', this.value)" min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Flat Price (৳)</label>
                    <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${tier.flat_price}" 
                        onchange="updateTier(${index}, 'flat_price', this.value)" min="0" step="0.01">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Label (Bangla)</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${tier.label}" 
                        onchange="updateTier(${index}, 'label', this.value)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Free Product (Optional)</label>
                    <select class="w-full px-3 py-2 border rounded-lg" onchange="updateTier(${index}, 'free_product_id', this.value)">
                        <option value="">None</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}" ${tier.free_product_id == {{ $p->id }} ? 'selected' : ''}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${tier.sort_order || 0}" 
                        onchange="updateTier(${index}, 'sort_order', this.value)" min="0">
                </div>
                <div class="col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" ${tier.is_active ? 'checked' : ''} onchange="updateTier(${index}, 'is_active', this.checked)">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
        </div>
        `;
    });
    tiersContainer.innerHTML = html;
}

function addTier() {
    const quantity = tiers.length > 0 ? Math.max(...tiers.map(t => t.quantity)) + 1 : 1;
    tiers.push({
        quantity: quantity,
        flat_price: 0,
        label: quantity + 'টা কিনুন',
        is_active: true,
        sort_order: quantity
    });
    renderTiers();
}

function updateTier(index, field, value) {
    if (field === 'quantity') value = parseInt(value);
    if (field === 'flat_price') value = parseFloat(value);
    if (field === 'sort_order') value = parseInt(value);
    tiers[index][field] = value;
}

function removeTier(index) {
    if (tiers[index].id) {
        if (!confirm('Delete this tier?')) return;
        tiers[index]._delete = true;
    }
    tiers.splice(index, 1);
    renderTiers();
}

function saveTiers() {
    const activeTiers = tiers.filter(t => !t._delete).map(t => ({
        quantity: t.quantity,
        flat_price: t.flat_price,
        label: t.label,
        free_product_id: t.free_product_id || null,
        is_active: t.is_active,
        sort_order: t.sort_order || 0
    }));

    fetch('/api/volume-discounts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            product_id: selectedProductId,
            tiers: activeTiers
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Saved successfully!');
        loadTiers(selectedProductId);
    })
    .catch(err => alert('Error saving: ' + err));
}
</script>
@endpush