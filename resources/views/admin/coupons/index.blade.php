@extends('admin.layouts.master')

@section('header', 'Coupons')

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Header with Create Button -->
    <div class="p-6 border-b border-gray-100 flex justify-between items-center flex-wrap gap-3">
        <h2 class="text-lg font-semibold text-gray-800">All Coupons</h2>
        <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition text-sm">
            <i class="fas fa-plus mr-1"></i>Create
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex flex-wrap gap-3">
            <input
                type="text"
                name="search"
                placeholder="Search coupons..."
                value="{{ request('search') }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500 min-w-[150px]"
            >
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                Filter
            </button>
        </form>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valid Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($coupons as $coupon)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900">{{ $coupon->code }}</span>
                            @if($coupon->is_featured)
                            <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded">Featured</span>
                            @endif
                        </div>
                        @if($coupon->description)
                        <p class="text-sm text-gray-500">{{ $coupon->description }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($coupon->discount_type === 'percentage')
                        <span class="text-rose-600 font-medium">{{ $coupon->discount_value }}%</span>
                        @else
                        <span class="text-rose-600 font-medium">৳{{ $coupon->discount_value }}</span>
                        @endif
                        @if($coupon->max_discount_amount)
                        <p class="text-xs text-gray-500">Max: ৳{{ $coupon->max_discount_amount }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $coupon->min_order_amount ? '৳' . $coupon->min_order_amount : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($coupon->start_date)
                        <div>{{ $coupon->start_date->format('M d, Y') }}</div>
                        @endif
                        @if($coupon->expire_date)
                        <div class="{{ now()->gt($coupon->expire_date) ? 'text-red-500' : '' }}">
                            {{ $coupon->expire_date->format('M d, Y') }}
                        </div>
                        @else
                        <div class="text-gray-400">No expiry</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($coupon->is_active)
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                        @else
                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-yellow-600 hover:text-yellow-800 mr-3">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No coupons found. <a href="{{ route('admin.coupons.create') }}" class="text-rose-500 hover:underline">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($coupons as $coupon)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Coupon Header -->
            <div class="flex justify-between items-start mb-2">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-900">{{ $coupon->code }}</span>
                    @if($coupon->is_featured)
                    <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded">Featured</span>
                    @endif
                </div>
                <span class="px-2 py-1 text-xs rounded-full @if($coupon->is_active) bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">
                    @if($coupon->is_active) Active @else Inactive @endif
                </span>
            </div>

            @if($coupon->description)
            <p class="text-sm text-gray-500 mb-2">{{ $coupon->description }}</p>
            @endif

            <!-- Discount -->
            <div class="mb-2">
                <span class="text-xl font-bold text-rose-600">
                    @if($coupon->discount_type === 'percentage')
                    {{ $coupon->discount_value }}% OFF
                    @else
                    ৳{{ $coupon->discount_value }} OFF
                    @endif
                </span>
                @if($coupon->max_discount_amount)
                <span class="text-xs text-gray-500 ml-2">(Max: ৳{{ $coupon->max_discount_amount }})</span>
                @endif
            </div>

            <!-- Details -->
            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                <span>Min: {{ $coupon->min_order_amount ? '৳' . $coupon->min_order_amount : '৳0' }}</span>
                <span>{{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }} used</span>
            </div>

            <!-- Expiry -->
            <div class="text-xs text-gray-500 mb-3 pb-3 border-b border-gray-100">
                @if($coupon->start_date)
                <span>{{ $coupon->start_date->format('M d, Y') }}</span>
                @endif
                @if($coupon->expire_date)
                - <span class="{{ now()->gt($coupon->expire_date) ? 'text-red-500' : '' }}">{{ $coupon->expire_date->format('M d, Y') }}</span>
                @else
                <span class="text-gray-400">No expiry</span>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-yellow-50">
                    <i class="fas fa-edit"></i>Edit
                </a>
                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            No coupons found. <a href="{{ route('admin.coupons.create') }}" class="text-rose-500 hover:underline">Create one</a>
        </div>
        @endforelse
    </div>
    
    @if($coupons->hasPages())
    <div class="p-6 border-t border-gray-100">
        {{ $coupons->links() }}
    </div>
    @endif
</div>
@endsection
