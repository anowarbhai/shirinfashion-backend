@extends('admin.layouts.master')

@section('title', 'Sliders - Shirin Fashion Admin')
@section('header', 'Hero Sliders')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Hero Sliders</h2>
                <p class="text-gray-500 text-sm mt-1">Manage homepage hero banner</p>
            </div>
            <a href="{{ route('admin.sliders.create') }}" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition flex items-center gap-2 text-sm">
                <i class="fas fa-plus"></i>Add
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Content</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Button</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sliders as $slider)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-200 text-gray-700 rounded-full text-sm font-medium">
                            {{ $slider->order }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="relative w-32 h-20 rounded-lg overflow-hidden bg-gray-100">
                            <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $slider->title ?? 'No Title' }}</p>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($slider->subtitle, 60) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($slider->button_text)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $slider->button_color_classes }}">
                                {{ $slider->button_text }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{ Str::limit($slider->button_link, 30) }}</p>
                        @else
                            <span class="text-gray-400 text-sm">No button</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form method="POST" action="{{ route('admin.sliders.toggle', $slider) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $slider->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $slider->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-1">{{ $slider->is_active ? 'Active' : 'Inactive' }}</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this slider?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-2 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-images text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No sliders yet</h3>
                            <p class="text-gray-500 mb-4">Create your first hero banner slider to display on the homepage</p>
                            <a href="{{ route('admin.sliders.create') }}" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition">
                                <i class="fas fa-plus mr-2"></i>Add Slider
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($sliders as $slider)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Slider Image -->
            <div class="relative w-full h-32 rounded-lg overflow-hidden bg-gray-100 mb-3">
                <img src="{{ $slider->image_url }}" alt="{{ $slider->title }}" class="w-full h-full object-cover">
            </div>

            <!-- Order Badge -->
            <div class="absolute top-2 left-2 w-8 h-8 bg-gray-800/70 text-white rounded-full flex items-center justify-center text-sm font-medium">
                {{ $slider->order }}
            </div>

            <!-- Content -->
            <div class="mb-2">
                <h3 class="font-semibold text-gray-900">{{ $slider->title ?? 'No Title' }}</h3>
                <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($slider->subtitle, 80) }}</p>
            </div>

            <!-- Button -->
            @if($slider->button_text)
            <div class="mb-3 pb-3 border-b border-gray-100">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $slider->button_color_classes }}">
                    {{ $slider->button_text }}
                </span>
            </div>
            @endif

            <!-- Status & Actions -->
            <div class="flex items-center justify-between">
                <form method="POST" action="{{ route('admin.sliders.toggle', $slider) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $slider->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $slider->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </form>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.sliders.edit', $slider) }}" class="text-blue-600 hover:text-blue-800 p-2">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" class="inline" onsubmit="return confirm('Are you sure?');">
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
            <i class="fas fa-images text-4xl text-gray-300 mb-3 block"></i>
            <p class="text-lg font-medium">No sliders yet</p>
        </div>
        @endforelse
    </div>

    <div class="p-6 border-t border-gray-200">
        {{ $sliders->links() }}
    </div>
</div>
@endsection
