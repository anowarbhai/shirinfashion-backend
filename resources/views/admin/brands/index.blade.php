@extends('admin.layouts.master')

@section('title', 'Brands - Shirin Fashion Admin')
@section('header', 'Brands')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <form method="GET" class="relative flex-1 md:flex-none">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brands..."
                class="w-full md:w-64 px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            <button type="submit" class="absolute right-0 top-0 h-full px-4 bg-rose-600 text-white rounded-r-lg hover:bg-rose-700 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('admin.brands.create') }}" class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition font-medium flex items-center gap-2">
            <i class="fas fa-plus"></i>Add
        </a>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b">
                <th class="text-left py-3 px-4">Logo</th>
                <th class="text-left py-3 px-4">Name</th>
                <th class="text-left py-3 px-4">Slug</th>
                <th class="text-left py-3 px-4">Status</th>
                <th class="text-left py-3 px-4">Products</th>
                <th class="text-left py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 px-4">
                    @if($brand->logo)
                    <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="h-10 w-10 object-contain">
                    @else
                    <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                    @endif
                </td>
                <td class="py-3 px-4">{{ $brand->name }}</td>
                <td class="py-3 px-4 text-gray-500">{{ $brand->slug }}</td>
                <td class="py-3 px-4">
                    @if($brand->is_active)
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Active</span>
                    @else
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4">{{ $brand->products()->count() }}</td>
                <td class="py-3 px-4">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="text-blue-600 hover:text-blue-800 mr-3">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-500">No brands found</td>
            </tr>
            @endforelse
</tbody>
    </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
        @forelse($brands as $brand)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    @if($brand->logo)
                    <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="w-14 h-14 object-contain rounded-lg">
                    @else
                    <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-xl"></i>
                    </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 line-clamp-2">{{ $brand->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $brand->slug }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full @if($brand->is_active) bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">
                        @if($brand->is_active) Active @else Inactive @endif
                    </span>
                    <span class="text-xs text-gray-600">{{ $brand->products()->count() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-yellow-50"><i class="fas fa-edit"></i>Edit</a>
                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">No brands found</div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $brands->links() }}
    </div>
</div>
@endsection
