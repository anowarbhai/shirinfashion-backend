@extends('admin.layouts.master')

@section('title', 'Pages')

@section('header', 'Pages')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <h2 class="text-xl font-semibold">All Pages</h2>
        <a href="{{ route('admin.pages.create') }}" class="bg-rose-500 text-white px-4 py-2 rounded-lg hover:bg-rose-600 transition text-sm">
            <i class="fas fa-plus mr-1"></i>Add
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pages as $page)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $page->title }}</td>
                    <td class="px-6 py-4 text-gray-500">/page/{{ $page->slug }}</td>
                    <td class="px-6 py-4">
                        @if($page->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $page->sort_order }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.pages.builder', $page) }}" class="text-purple-600 hover:text-purple-800" title="Page Builder">
                                <i class="fas fa-th-large"></i>
                            </a>
                            <a href="{{ route('admin.pages.show', $page) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.pages.edit', $page) }}" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No pages found. <a href="{{ route('admin.pages.create') }}" class="text-rose-500 hover:text-rose-600">Create your first page</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
        @forelse($pages as $page)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">{{ $page->title }}</h3>
                <span class="px-2 py-1 text-xs font-semibold rounded-full @if($page->is_active) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                    @if($page->is_active) Active @else Inactive @endif
                </span>
            </div>

            <!-- Slug -->
            <p class="text-sm text-gray-500 mb-3 pb-3 border-b border-gray-100">/page/{{ $page->slug }}</p>

            <!-- Footer -->
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">Order: {{ $page->sort_order }}</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.pages.builder', $page) }}" class="text-purple-600 hover:text-purple-800 p-2">
                        <i class="fas fa-th-large"></i>
                    </a>
                    <a href="{{ route('admin.pages.show', $page) }}" class="text-blue-600 hover:text-blue-800 p-2">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.pages.edit', $page) }}" class="text-yellow-600 hover:text-yellow-800 p-2">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            No pages found.
        </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $pages->links() }}
    </div>
</div>
@endsection
