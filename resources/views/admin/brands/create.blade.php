@extends('admin.layouts.master')

@section('title', 'Add Brand - Shirin Fashion Admin')
@section('header', 'Add Brand')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.brands.store') }}">
        @csrf
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    oninput="generateSlug(this.value)">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <p class="text-gray-500">Select from Media Library</p>
                        </div>
                        <p class="text-xs text-gray-500">Recommended: Square image, 200x200px or larger</p>
                    </div>
                </div>
                <div class="mt-3 flex gap-3">
                    <button type="button" onclick="openMediaSelector(handleBrandLogoSelect, 'single')" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 text-sm">
                        <i class="fas fa-images mr-2"></i> Choose Logo
                    </button>
                </div>
                <input type="hidden" name="logo" id="logo-input" value="{{ old('logo') }}">
                <div id="logo-preview" class="mt-3 {{ old('logo') ? '' : 'hidden' }}">
                    <div class="relative inline-block">
                        <img id="logo-preview-img" src="{{ old('logo') }}" alt="Logo Preview" class="h-24 object-contain rounded-lg border">
                        <button type="button" onclick="removeLogo()" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                <label class="ml-2 text-sm text-gray-700">Active</label>
            </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.brands.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">
                Save Brand
            </button>
        </div>
    </form>
</div>

@include('admin.components.media-selector')

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.getElementById('name').addEventListener('input', function() {
        const name = this.value;
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
        document.getElementById('slug').value = slug;
    });

    function handleBrandLogoSelect(url) {
        document.getElementById('logo-input').value = url;
        const preview = document.getElementById('logo-preview');
        const img = document.getElementById('logo-preview-img');
        img.src = url;
        preview.classList.remove('hidden');
    }

    function removeLogo() {
        document.getElementById('logo-input').value = '';
        document.getElementById('logo-preview').classList.add('hidden');
    }
</script>
@endsection
