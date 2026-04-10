@extends('admin.layouts.master')

@section('title', $page->title)

@section('header', 'Page Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">{{ $page->title }}</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.pages.edit', $page) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.pages.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Content</h3>
                <div class="prose max-w-none">
                    {!! $page->content ?: '<p class="text-gray-500">No content</p>' !!}
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Details</h3>
                <div class="space-y-4">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Status</span>
                        @if($page->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Slug</span>
                        <span class="font-medium">/page/{{ $page->slug }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Sort Order</span>
                        <span class="font-medium">{{ $page->sort_order }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Created</span>
                        <span class="font-medium">{{ $page->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Updated</span>
                        <span class="font-medium">{{ $page->updated_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">SEO</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-600 text-sm">Meta Title</span>
                        <p class="font-medium">{{ $page->meta_title ?: 'Not set' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 text-sm">Meta Description</span>
                        <p class="text-sm">{{ $page->meta_description ?: 'Not set' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
