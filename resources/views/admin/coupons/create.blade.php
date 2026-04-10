@extends('admin.layouts.master')

@section('header', 'Create Coupon')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.coupons.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code *</label>
                <input type="text" name="code" value="{{ old('code') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500 uppercase"
                    placeholder="e.g., SUMMER2024">
                @error('code')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" name="description" value="{{ old('description') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="Short description">
            </div>

            <!-- Discount Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                <select name="discount_type" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                    <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount (৳)</option>
                </select>
            </div>

            <!-- Discount Value -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value *</label>
                <input type="number" name="discount_value" value="{{ old('discount_value') }}" required min="0" step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="0.00">
            </div>

            <!-- Min Order Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Order Amount</label>
                <input type="number" name="min_order_amount" value="{{ old('min_order_amount') }}" min="0" step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="0.00">
            </div>

            <!-- Max Discount Amount -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Discount Amount</label>
                <input type="number" name="max_discount_amount" value="{{ old('max_discount_amount') }}" min="0" step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="0.00">
            </div>

            <!-- Usage Limit -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Usage Limit</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="Leave empty for unlimited">
            </div>

            <!-- Max Uses Per User -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Max Uses Per User</label>
                <input type="number" name="max_uses_per_user" value="{{ old('max_uses_per_user') }}" min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="Leave empty for unlimited">
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>

            <!-- Expire Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expire Date</label>
                <input type="date" name="expire_date" value="{{ old('expire_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>

            <!-- Status -->
            <div class="flex items-center gap-6 mt-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>

                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Featured</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg hover:bg-rose-600">
                Create Coupon
            </button>
            <a href="{{ route('admin.coupons.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
