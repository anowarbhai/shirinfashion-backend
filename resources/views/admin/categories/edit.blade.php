@extends('admin.layouts.master')

@section('title', 'Edit Category - Shirin Fashion Admin')
@section('header', 'Edit Category')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">{{ old('description', $category->description) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="category-image" class="relative cursor-pointer bg-white rounded-md font-medium text-rose-600 hover:text-rose-500 focus-within:outline-none">
                                <span>Upload a file</span>
                                <input id="category-image" name="category_image" type="file" class="sr-only" accept="image/*">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
                <div class="mt-3 flex gap-3">
                    <button type="button" onclick="openMediaSelector(handleCategoryImageSelect, 'single')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-images mr-2"></i> Choose from Library
                    </button>
                </div>
                <input type="hidden" name="image" id="image-input" value="{{ old('image', $category->image) }}">
                <div id="image-preview" class="mt-3 {{ $category->image ? '' : 'hidden' }}">
                    <div class="relative inline-block">
                        <img id="image-preview-img" src="{{ str_starts_with($category->image, 'http') ? $category->image : asset($category->image) }}" alt="Category Image" class="w-32 h-32 object-cover rounded-lg border">
                        <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="w-4 h-4 text-rose-600 border-gray-300 rounded"><label class="ml-2 text-sm text-gray-700">Active</label></div>
        </div>
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Update Category</button>
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

    document.getElementById('category-image').addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('image', file);
        
        try {
            const response = await fetch('{{ route("admin.upload.image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                handleCategoryImageSelect(data.url);
            } else {
                alert(data.message || 'Upload failed');
            }
        } catch (error) {
            console.error('Upload failed:', error);
            alert('Upload failed');
        }
    });

    function handleCategoryImageSelect(url) {
        document.getElementById('image-input').value = url;
        document.getElementById('image-preview').classList.remove('hidden');
        // Handle URL correctly - remove double slashes
        let imgSrc = url;
        if (!imgSrc.startsWith('http')) {
            imgSrc = '/' + imgSrc.replace(/^\/+/, ''); // Remove any leading slashes and add one
        }
        document.getElementById('image-preview-img').src = imgSrc;
    }

    function removeImage() {
        document.getElementById('image-input').value = '';
        document.getElementById('image-preview').classList.add('hidden');
    }
</script>
@endsection
