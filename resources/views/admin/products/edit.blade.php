@extends('admin.layouts.master')

@section('title', 'Edit Product - Shirin Fashion Admin')
@section('header', 'Edit Product')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Category *</label>
                <select name="category_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Categories</label>
                <div class="flex flex-wrap gap-2 max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-3">
                    @php
                        $selectedCategoryIds = $product->categories->pluck('id')->toArray();
                    @endphp
                    @foreach($categories as $category)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" 
                                {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}
                                class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1">Select additional categories (primary category is required)</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sale Price</label>
                <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Manage Stock</label>
                <div class="flex items-center">
                    <input type="checkbox" name="manage_stock" id="manage_stock" value="1" {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 border-gray-300 rounded" onchange="toggleStockFields()">
                    <span class="ml-2 text-sm text-gray-700">Enable stock management</span>
                </div>
            </div>

            <div id="stock_quantity_field" class="{{ old('manage_stock', $product->manage_stock) ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            </div>

            <div id="stock_status_field" class="{{ old('manage_stock', $product->manage_stock) ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Status</label>
                <select name="stock_status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                    <option value="instock" {{ old('stock_status', $product->stock_status ?? 'instock') == 'instock' ? 'selected' : '' }}>In Stock</option>
                    <option value="outofstock" {{ old('stock_status', $product->stock_status ?? 'instock') == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="backorder" {{ old('stock_status', $product->stock_status ?? 'instock') == 'backorder' ? 'selected' : '' }}>Backorder</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select name="brand_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
            <div id="short-description-editor" style="height: 120px;">
                {!! old('short_description', $product->short_description) !!}
            </div>
            <input type="hidden" name="short_description" id="short_description" value="{{ old('short_description', $product->short_description) }}">
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <div id="description-editor" style="height: 200px;">
                {!! old('description', $product->description) !!}
            </div>
            <input type="hidden" name="description" id="description" value="{{ old('description', $product->description) }}">
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Image (First Image)</label>
            <div class="flex gap-3 mb-2">
                <button type="button" onclick="openMediaForThumbnail()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                    <i class="fas fa-images mr-2"></i> Choose from Library
                </button>
                <span class="flex items-center text-sm text-gray-500">or</span>
                <label for="thumbnail-file" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-sm cursor-pointer">
                    <i class="fas fa-upload mr-2"></i> Upload
                    <input id="thumbnail-file" name="thumbnail_file" type="file" class="hidden" accept="image/*">
                </label>
            </div>
            <input type="hidden" name="image" id="image-input" value="{{ old('image', $product->image) }}">
            <div id="thumbnail-preview" class="mt-2 {{ $product->image ? '' : 'hidden' }}">
                <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset($product->image) }}" alt="Thumbnail" class="w-32 h-32 object-cover rounded">
                <div class="flex gap-2 mt-2">
                    <button type="button" onclick="openMediaForThumbnail()" class="text-sm text-rose-500 hover:text-rose-600">Change</button>
                    <button type="button" onclick="removeThumbnail()" class="text-sm text-red-600 hover:text-red-800">Remove</button>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Gallery Images (For Product Page)</label>
            <div class="flex gap-3 mb-2">
                <button type="button" onclick="openMediaForGallery()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm">
                    <i class="fas fa-images mr-2"></i> Choose from Library
                </button>
                <label for="gallery-files" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-sm cursor-pointer">
                    <i class="fas fa-upload mr-2"></i> Upload Multiple
                    <input id="gallery-files" name="gallery_files" type="file" class="hidden" accept="image/*" multiple>
                </label>
            </div>
            <input type="hidden" name="images" id="images-input" value="{{ old('images', $product->images ? json_encode($product->images) : '[]') }}">
            <div id="gallery-preview" class="mt-2 flex flex-wrap gap-2">
                @php
                    $galleryImages = $product->images ?? [];
                @endphp
                @if(!empty($galleryImages))
                    @foreach($galleryImages as $img)
                        @php
                            $imgSrc = $img;
                            if (!str_starts_with($img, 'http')) {
                                $imgSrc = url(ltrim($img, '/'));
                            }
                        @endphp
                        <div class="relative group" data-url="{{ $img }}">
                            <img src="{{ $imgSrc }}" class="w-20 h-20 object-cover rounded">
                            <button type="button" onclick="removeGalleryImage(this, '{{ addslashes($img) }}')" class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity -mt-1 -mr-1">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                    class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                <label class="ml-2 text-sm text-gray-700">Featured Product</label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                <label class="ml-2 text-sm text-gray-700">Active</label>
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tag_ids', $product->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                            class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Attributes</label>
            @foreach($attributes as $attribute)
                <div class="mb-3">
                    <span class="text-sm font-medium text-gray-600">{{ $attribute->name }}:</span>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($attribute->values as $value)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="attribute_value_ids[]" value="{{ $value->id }}" {{ in_array($value->id, old('attribute_value_ids', $product->attributeValues->pluck('id')->toArray())) ? 'checked' : '' }}
                                    class="w-4 h-4 text-rose-600 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">{{ $value->value }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" id="submit-btn" class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">
                Update Product
            </button>
        </div>
    </form>
</div>

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
    
    document.getElementById('thumbnail-file').addEventListener('change', async function(e) {
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
            console.log('Upload response:', data);
            if (data.success) {
                document.getElementById('image-input').value = data.url;
                document.getElementById('thumbnail-preview').classList.remove('hidden');
                document.getElementById('thumbnail-preview').querySelector('img').src = data.url;
            } else {
                alert(data.message || 'Upload failed');
            }
        } catch (error) {
            console.error('Upload failed:', error);
            alert('Upload failed');
        }
    });
    
    document.getElementById('gallery-files').addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;
        
        const formData = new FormData();
        files.forEach(file => formData.append('images[]', file));
        
        try {
            const response = await fetch('{{ route("admin.upload.images") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });
            const data = await response.json();
            console.log('Gallery upload response:', data);
            if (data.success) {
                const imagesInput = document.getElementById('images-input');
                const currentImages = imagesInput.value ? JSON.parse(imagesInput.value) : [];
                const allImages = [...currentImages, ...data.urls];
                imagesInput.value = JSON.stringify(allImages);
                
                const previewContainer = document.getElementById('gallery-preview');
                data.urls.forEach(url => {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `<img src="${url}" class="w-20 h-20 object-cover rounded">`;
                    previewContainer.appendChild(div);
                });
            } else {
                alert(data.message || 'Upload failed');
            }
        } catch (error) {
            console.error('Upload failed:', error);
            alert('Upload failed');
        }
    });
    
    function removeThumbnail() {
        document.getElementById('image-input').value = '';
        document.getElementById('thumbnail-preview').classList.add('hidden');
    }

    function toggleStockFields() {
        const manageStock = document.getElementById('manage_stock').checked;
        document.getElementById('stock_quantity_field').classList.toggle('hidden', !manageStock);
        document.getElementById('stock_status_field').classList.toggle('hidden', manageStock);
    }

    // Initialize Quill editors
    var quillShortDesc = new Quill('#short-description-editor', {
        theme: 'snow',
        placeholder: 'Enter short description...',
        modules: {
            toolbar: [['bold', 'italic'], ['link']]
        }
    });
    var shortDescInput = document.getElementById('short_description');
    if (shortDescInput.value) {
        quillShortDesc.root.innerHTML = shortDescInput.value;
    }

    var quillDesc = new Quill('#description-editor', {
        theme: 'snow',
        placeholder: 'Enter description...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'header': [1, 2, 3, false] }],
                ['link'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }]
            ]
        }
    });
    var descInput = document.getElementById('description');
    if (descInput.value) {
        quillDesc.root.innerHTML = descInput.value;
    }

    // Update hidden inputs and submit form
    var submitBtn = document.getElementById('submit-btn');
    submitBtn.addEventListener('click', function(e) {
        var shortDescVal = quillShortDesc.root.innerHTML;
        var descVal = quillDesc.root.innerHTML;
        
        // Log for debugging
        console.log('Short Desc:', shortDescVal);
        console.log('Desc:', descVal);
        
        // Set values
        shortDescInput.value = shortDescVal;
        descInput.value = descVal;
        
        // Small delay then submit
        var form = submitBtn.closest('form');
        form.submit();
    });
</script>

<!-- Media Selector -->
@include('admin.components.media-selector')

<script>
function openMediaForThumbnail() {
    openMediaSelector((url) => {
        document.getElementById('image-input').value = url;
        document.getElementById('thumbnail-preview').classList.remove('hidden');
        // Handle both full URLs and relative paths with or without leading slash
        const imgSrc = url.startsWith('http') ? url : window.location.origin + (url.startsWith('/') ? url : '/' + url);
        document.getElementById('thumbnail-preview').querySelector('img').src = imgSrc;
    }, 'single');
}

function openMediaForGallery() {
    openMediaSelector(null, 'gallery');
}

function removeGalleryImage(btn, url) {
    // Remove from DOM
    btn.closest('.relative').remove();
    
    // Remove from hidden input
    const input = document.getElementById('images-input');
    let urls = input.value ? JSON.parse(input.value) : [];
    // Handle both full URLs and relative paths
    urls = urls.filter(u => {
        if (u === url) return false;
        // Also check if the URL ends match (for relative paths)
        if (u.endsWith(url) || url.endsWith(u)) return false;
        return true;
    });
    input.value = JSON.stringify(urls);
}
</script>

<!-- Media Selector -->
@include('admin.components.media-selector')
@endsection
