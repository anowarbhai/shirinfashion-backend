@extends('admin.layouts.master')

@section('title', 'Orders - Shirin Fashion Admin')
@section('header', 'Orders')

@php
use App\Models\GeneralSetting;
$generalSettings = GeneralSetting::getSettings();
$timezone = $generalSettings->timezone ?? 'Asia/Dhaka';
$dateFormat = $generalSettings->date_format ?? 'M d, Y';
$timeFormat = $generalSettings->time_format ?? 'h:i A';
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
        <form method="GET" class="flex items-center gap-3 flex-wrap relative">
            <input type="text" name="search" placeholder="Search orders..." value="{{ request('search') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500 w-full md:w-auto">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                <option value="">All Status</option>
                <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700"><i class="fas fa-search"></i></button>
        </form>
        
        <div class="flex gap-2">
            <button type="button" id="bulkDeleteBtn" onclick="bulkDelete()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-sm" disabled>
                <i class="fas fa-trash mr-1"></i>Delete
            </button>
            <a href="{{ route('admin.orders.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700 text-sm">
                <i class="fas fa-plus mr-1"></i>Create
            </a>
        </div>
    </div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <form id="bulkDeleteForm" method="POST" action="/admin/delete-orders-bulk">
            @csrf
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)" class="w-4 h-4">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="order-checkbox w-4 h-4" onchange="updateBulkDeleteBtn()">
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">#{{ $order->id }}</td>
                    <td class="px-6 py-4">
                        <p class="text-gray-800">{{ $order->customer_name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ formatCurrencyAdmin($order->total, $currencySymbol, $currencyPosition) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs @if($order->payment_status === 'paid') bg-green-100 text-green-700 @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs @if($order->status === 'incomplete') bg-red-100 text-red-700 @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700 @elseif($order->status === 'processing') bg-blue-100 text-blue-700 @elseif($order->status === 'shipped') bg-purple-100 text-purple-700 @elseif($order->status === 'delivered') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                            @if($order->status === 'incomplete')
                            ⚠️ Incomplete
                            @else
                            {{ ucfirst($order->status) }}
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">
                        <span>{{ \Carbon\Carbon::parse($order->created_at)->setTimezone($timezone)->format($dateFormat) }}</span>
                        <span class="text-xs block">{{ \Carbon\Carbon::parse($order->created_at)->setTimezone($timezone)->format($timeFormat) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <button type="button" onclick="checkCustomerRate('{{ $order->customer_phone }}')" class="bg-rose-100 text-rose-700 px-3 py-1 rounded text-xs hover:bg-rose-200">
                            View Rate
                        </button>
                    </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                            </div>
                        </td>
                    </tr>
                    @if($orders->isEmpty())
                    <tr><td colspan="9" class="px-6 py-8 text-center text-gray-500">No orders found</td></tr>
                    @endif
                </tbody>
            </table>
        </form>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($orders as $order)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition relative">
            <!-- Checkbox -->
            <div class="absolute top-4 left-4">
                <input type="checkbox" name="ids[]" value="{{ $order->id }}" class="order-checkbox w-4 h-4" onchange="updateBulkDeleteBtn()">
            </div>
            
<!-- Order Header -->
            <div class="flex justify-between items-start mb-2 pl-8">
                <div>
                    <span class="font-semibold text-gray-800">Order #{{ $order->id }}</span>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full @if($order->status === 'incomplete') bg-red-100 text-red-700 @elseif($order->status === 'pending') bg-yellow-100 text-yellow-700 @elseif($order->status === 'processing') bg-blue-100 text-blue-700 @elseif($order->status === 'shipped') bg-purple-100 text-purple-700 @elseif($order->status === 'delivered') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                    @if($order->status === 'incomplete') ⚠️ Incomplete @else {{ ucfirst($order->status) }} @endif
                </span>
            </div>

            <!-- Customer Info -->
            <div class="mb-2">
                <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->customer_phone }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ \Carbon\Carbon::parse($order->created_at)->setTimezone($timezone)->format($dateFormat . ' ' . $timeFormat) }}</p>
            </div>

            <!-- Order Details -->
            <div class="flex items-center justify-between text-sm mb-3 pb-3 border-b border-gray-100">
                <span class="font-bold text-lg text-gray-800">{{ formatCurrencyAdmin($order->total, $currencySymbol, $currencyPosition) }}</span>
                <span class="px-2 py-1 text-xs rounded-full @if($order->payment_status === 'paid') bg-green-100 text-green-700 @else bg-yellow-100 text-yellow-700 @endif">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <button type="button" onclick="checkCustomerRate('{{ $order->customer_phone }}')" class="bg-rose-100 text-rose-700 px-3 py-2 rounded-lg text-xs font-medium hover:bg-rose-200">
                    View Rate
                </button>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 p-2">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
            @if($orders->isEmpty())
            <div class="p-8 text-center text-gray-500">No orders found</div>
            @endif
        </div>
    </div>
    
    @if($orders->hasPages())
    <div class="p-6 border-t border-gray-100">{{ $orders->links() }}</div>
    @endif
</div>

<div id="rateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Customer Rate Check</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <span class="text-gray-600">Phone: </span>
                <span id="modalPhone" class="font-medium text-lg"></span>
            </div>
            <div id="rateSummary" class="hidden mb-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <span id="rateStatusBadge" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                        <span class="text-gray-600 font-medium">Score: <span id="rateScore" class="font-bold text-lg"></span></span>
                    </div>
                    <div class="flex gap-6 text-center">
                        <div>
                            <div class="text-2xl font-bold" id="rateTotal">0</div>
                            <div class="text-xs text-gray-500">মোট</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600" id="rateSuccess">0</div>
                            <div class="text-xs text-gray-500">সফল</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600" id="rateCancel">0</div>
                            <div class="text-xs text-gray-500">বাতিল</div>
                        </div>
                    </div>
                </div>
            </div>
            <table id="rateTable" class="w-full text-sm border-collapse hidden">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border px-3 py-2 text-left">কুরিয়ার</th>
                        <th class="border px-3 py-2 text-center">মোট</th>
                        <th class="border px-3 py-2 text-center">সফল</th>
                        <th class="border px-3 py-2 text-center">বাতিল</th>
                        <th class="border px-3 py-2 text-center">রেট</th>
                    </tr>
                </thead>
                <tbody id="rateTableBody"></tbody>
            </table>
            <div id="rateLoading" class="text-center py-8 text-gray-500">Loading...</div>
            <div id="rateError" class="text-center py-8 text-red-500 hidden"></div>
        </div>
    </div>
</div>

<script>
function checkCustomerRate(phone) {
    document.getElementById('modalPhone').textContent = phone;
    document.getElementById('rateModal').classList.remove('hidden');
    document.getElementById('rateSummary').classList.add('hidden');
    document.getElementById('rateTable').classList.add('hidden');
    document.getElementById('rateLoading').classList.remove('hidden');
    document.getElementById('rateError').classList.add('hidden');

    fetch('{{ route('admin.settings.fraud-checker.test') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ phone: phone })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('rateLoading').classList.add('hidden');
        
        if (data.error) {
            document.getElementById('rateError').textContent = data.error;
            document.getElementById('rateError').classList.remove('hidden');
            return;
        }
        
        document.getElementById('rateSummary').classList.remove('hidden');
        document.getElementById('rateTable').classList.remove('hidden');
        
        const isSafe = data.status === 'Safe' || data.status === 'safe';
        const statusBadge = document.getElementById('rateStatusBadge');
        if (isSafe) {
            statusBadge.textContent = '✅ নিরাপদ';
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
        } else {
            statusBadge.textContent = '⚠️ ' + (data.status || 'ঝুঁকিপূর্ণ');
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
        }
        
        document.getElementById('rateScore').textContent = (data.score || 0) + '%';
        document.getElementById('rateTotal').textContent = data.total_parcel || 0;
        document.getElementById('rateSuccess').textContent = data.success_parcel || 0;
        document.getElementById('rateCancel').textContent = data.cancel_parcel || 0;
        
        const response = data.response || {};
        const courierNames = {
            'pathao': 'Pathao',
            'steadfast': 'Steadfast',
            'redx': 'RedX',
            'carrybee': 'CarryBee'
        };
        
        let tableHTML = '';
        for (const [key, value] of Object.entries(response)) {
            if (!value || !value.data) continue;
            const name = courierNames[key] || key;
            const d = value.data;
            const total = d.total || 0;
            const success = d.success || 0;
            const cancelled = d.cancel || 0;
            const rate = d.deliveredPercentage || 0;
            
            tableHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="border px-3 py-2 font-medium">${name}</td>
                    <td class="border px-3 py-2 text-center">${total}</td>
                    <td class="border px-3 py-2 text-center text-green-600">${success}</td>
                    <td class="border px-3 py-2 text-center text-red-600">${cancelled}</td>
                    <td class="border px-3 py-2 text-center">${rate}%</td>
                </tr>
            `;
        }
        
        document.getElementById('rateTableBody').innerHTML = tableHTML || '<tr><td colspan="5" class="border px-3 py-2 text-center text-gray-500">No courier data</td></tr>';
    })
    .catch(error => {
        document.getElementById('rateLoading').classList.add('hidden');
        document.getElementById('rateError').textContent = 'Error: ' + error.message;
        document.getElementById('rateError').classList.remove('hidden');
    });
}

function closeModal() {
    document.getElementById('rateModal').classList.add('hidden');
}

document.getElementById('rateModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateBulkDeleteBtn();
}

function updateBulkDeleteBtn() {
    const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
    const btn = document.getElementById('bulkDeleteBtn');
    btn.disabled = checkedCount === 0;
    btn.textContent = checkedCount > 0 ? `Delete Selected (${checkedCount})` : 'Delete Selected';
}

function bulkDelete() {
    const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one order to delete');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${checkedCount} order(s)? This action cannot be undone.`)) {
        // Remove any _method hidden input
        const existingMethod = document.querySelector('input[name="_method"]');
        if (existingMethod) existingMethod.remove();
        
        const form = document.getElementById('bulkDeleteForm');
        form.method = 'POST';
        form.action = '/admin/delete-orders-bulk';
        form.submit();
    }
}
</script>
@endsection
