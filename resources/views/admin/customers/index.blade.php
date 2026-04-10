@extends('admin.layouts.master')

@section('title', 'Customers - Shirin Fashion Admin')
@section('header', 'Customers')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <h2 class="text-lg font-semibold text-gray-700">All Customers</h2>
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search..."
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500 w-full md:w-72"
                >
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($customers as $customer)
                @php
                    $latestOrder = $customer->orders->first();
                    $address = $latestOrder ? $latestOrder->shipping_address : null;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 font-bold mr-3">
                                {{ substr($customer->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $customer->name }}</p>
                                @if($customer->is_admin)
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded">Admin</span>
                                @else
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Customer</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900">{{ $customer->email }}</p>
                        <p class="text-sm text-gray-500">{{ $customer->phone ?? 'N/A' }}</p>
                        @if($address)
                        <p class="text-xs text-gray-400 mt-1 max-w-xs truncate" title="{{ $address }}">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($address, 50) }}
                        </p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900">{{ $customer->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $customer->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-gray-900">{{ $customer->orders->count() ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                            <p class="text-lg font-medium text-gray-600">No customers found</p>
                            <p class="text-sm text-gray-400 mt-1">Customers will appear here when they register or place orders</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($customers as $customer)
        @php
            $latestOrder = $customer->orders->first();
            $address = $latestOrder ? $latestOrder->shipping_address : null;
        @endphp
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Customer Info -->
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-rose-100 rounded-full flex items-center justify-center text-rose-600 font-bold text-lg flex-shrink-0">
                    {{ substr($customer->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $customer->name }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        @if($customer->is_admin)
                        <span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded">Admin</span>
                        @else
                        <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded">Customer</span>
                        @endif
                        <span class="text-sm text-gray-600">{{ $customer->orders->count() ?? 0 }} orders</span>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="text-sm text-gray-600 mb-2">
                <p>{{ $customer->email }}</p>
                <p class="text-gray-500">{{ $customer->phone ?? 'N/A' }}</p>
            </div>

            @if($address)
            <p class="text-xs text-gray-400 mb-3 pb-3 border-b border-gray-100">
                <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($address, 60) }}
            </p>
            @endif

            <!-- Footer -->
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">{{ $customer->created_at->format('M d, Y') }}</span>
                <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-blue-50">
                    <i class="fas fa-eye"></i> View
                </a>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-users text-4xl text-gray-300 mb-3 block"></i>
            <p class="text-lg font-medium">No customers found</p>
        </div>
        @endforelse
    </div>
    
    <div class="p-6 border-t border-gray-200">
        {{ $customers->links() }}
    </div>
</div>
@endsection
