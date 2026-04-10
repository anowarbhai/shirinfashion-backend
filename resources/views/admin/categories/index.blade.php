@extends('admin.layouts.master')

@section('title', 'Categories - Shirin Fashion Admin')
@section('header', 'Categories')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center flex-wrap gap-3">
        <form method="GET" class="relative flex-1 md:flex-none">
            <input type="text" name="search" placeholder="Search categories..." 
                value="{{ request('search') }}"
                class="w-full md:w-64 px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            <button type="submit" class="absolute right-0 top-0 h-full bg-rose-600 text-white px-4 rounded-r-lg hover:bg-rose-700 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('admin.categories.create') }}" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700 transition font-medium flex items-center gap-2">
            <i class="fas fa-plus"></i>Add
        </a>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $category)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        @if($category->image)
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-12 h-12 object-cover rounded">
                        @else
                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                            <i class="fas fa-tag text-gray-400"></i>
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $category->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $category->slug }}</td>
                    <td class="px-6 py-4">
                        @if($category->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Active</span>
                        @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No categories found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($categories as $category)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    @if($category->image)
                    <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-14 h-14 object-cover rounded-lg">
                    @else
                    <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tag text-gray-400 text-xl"></i>
                    </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 line-clamp-2">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $category->slug }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                <span class="px-2 py-1 text-xs font-semibold rounded-full @if($category->is_active) bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                    @if($category->is_active) Active @else Inactive @endif
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-yellow-50">
                        <i class="fas fa-edit"></i>Edit
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline">
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
        <div class="p-8 text-center text-gray-500">No categories found</div>
        @endforelse
    </div>
    
    @if($categories->hasPages())
    <div class="p-6 border-t border-gray-100">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
