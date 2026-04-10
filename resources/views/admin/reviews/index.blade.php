@extends('admin.layouts.master')

@section('title', 'Reviews - Shirin Fashion Admin')
@section('header', 'Reviews')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select name="rating" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                <option value="">All Ratings</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
            </select>
            <button type="submit" class="bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700">Filter</button>
        </form>
    </div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.products.show', $review->product) }}" class="text-rose-600 hover:underline">{{ $review->product->name ?? 'N/A' }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">
                            @if($review->user)
                                {{ $review->user->name }}
                            @else
                                {{ $review->customer_name }}
                            @endif
                        </p>
                        @if($review->user)
                            <p class="text-xs text-gray-500">{{ $review->user->phone ?? 'N/A' }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $review->comment }}</td>
                    <td class="px-6 py-4">
                        @if($review->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Active</span>
                        @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Inactive</span>
                        @endif
                        @if($review->is_verified)
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs ml-1">Verified</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_active" value="{{ $review->is_active ? '0' : '1' }}">
                                <button type="submit" class="text-{{ $review->is_active ? 'gray' : 'green' }}-600 hover:text-{{ $review->is_active ? 'gray' : 'green' }}-800" title="{{ $review->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-toggle-{{ $review->is_active ? 'on' : 'off' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No reviews found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($reviews as $review)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Product & Customer -->
            <div class="mb-2">
                <p class="font-semibold text-gray-800">{{ $review->product->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-600">
                    @if($review->user)
                        {{ $review->user->name }}
                    @else
                        {{ $review->customer_name }}
                    @endif
                </p>
            </div>

            <!-- Rating Stars -->
            <div class="flex text-yellow-400 mb-2">
                @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                @endfor
            </div>

            <!-- Comment -->
            <p class="text-sm text-gray-600 mb-3 line-clamp-3">{{ $review->comment }}</p>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    @if($review->is_active)
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Active</span>
                    @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Inactive</span>
                    @endif
                    @if($review->is_verified)
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">Verified</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="is_active" value="{{ $review->is_active ? '0' : '1' }}">
                        <button type="submit" class="inline-flex items-center gap-1 text-{{ $review->is_active ? 'gray' : 'green' }}-600 hover:text-{{ $review->is_active ? 'gray' : 'green' }}-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-toggle-{{ $review->is_active ? 'on' : 'off' }}"></i>
                        </button>
                    </form>
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">No reviews found</div>
        @endforelse
    </div>
    
    @if($reviews->hasPages())
    <div class="p-6 border-t border-gray-100">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
