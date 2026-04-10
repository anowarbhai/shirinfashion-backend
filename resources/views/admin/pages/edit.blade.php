@extends('admin.layouts.master')

@section('title', 'Edit Page')

@section('header', 'Edit Page')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-container { min-height: 200px; font-size: 14px; }
    .ql-editor { min-height: 200px; }
    .slug-exists { color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; }
    .slug-ok { color: #16a34a; font-size: 0.875rem; margin-top: 0.25rem; }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-6" id="page-form">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Page Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                    placeholder="Enter page title">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <div class="flex gap-2">
                    <div class="flex-1 flex items-center">
                        <span class="px-3 py-2 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-500">/page/</span>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            placeholder="page-slug">
                    </div>
                    <button type="button" id="generate-slug" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 text-sm">
                        Generate from Title
                    </button>
                </div>
                <p id="slug-status" class="mt-1"></p>
                <p class="text-sm text-gray-500 mt-1">URL will be: <span id="url-preview">{{ url('/page/' . $page->slug) }}</span></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                <div id="content-editor" class="bg-white border border-gray-300 rounded-lg">{!! old('content', $page->content) !!}</div>
                <input type="hidden" name="content" id="content-input">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="SEO title">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order) }}" min="0" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                <textarea name="meta_description" rows="3" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                    placeholder="SEO description">{{ old('meta_description', $page->meta_description) }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $page->is_active ? 'checked' : '' }}
                    class="rounded text-rose-500 focus:ring-rose-500">
                <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg hover:bg-rose-600 transition">
                    Update Page
                </button>
                <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const pageId = {{ $page->id }};
    let slugCheckTimeout = null;

    // Convert string to slug format
    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    // Check if slug exists
    function checkSlug(slug) {
        if (!slug) {
            document.getElementById('slug-status').innerHTML = '';
            return;
        }

        fetch('{{ route("admin.pages.check-slug") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ slug: slug, page_id: pageId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                document.getElementById('slug-status').innerHTML = '<span class="slug-exists">This slug is already in use. It will be auto-generated.</span>';
            } else {
                document.getElementById('slug-status').innerHTML = '<span class="slug-ok">Slug is available.</span>';
            }
            document.getElementById('url-preview').textContent = '{{ url("/page/") }}/' + slug;
        });
    }

    // Generate slug from title
    document.getElementById('generate-slug').addEventListener('click', function() {
        const title = document.getElementById('title').value;
        const slug = slugify(title);
        document.getElementById('slug').value = slug;
        checkSlug(slug);
    });

    // Auto-generate slug on title change (only if slug is empty)
    document.getElementById('title').addEventListener('input', function() {
        const slugInput = document.getElementById('slug');
        if (!slugInput.value || slugInput.dataset.auto === 'true') {
            const slug = slugify(this.value);
            slugInput.value = slug;
            slugInput.dataset.auto = 'true';
            
            clearTimeout(slugCheckTimeout);
            slugCheckTimeout = setTimeout(() => checkSlug(slug), 300);
        }
    });

    // Check slug on manual change
    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.auto = 'false';
        const slug = slugify(this.value);
        this.value = slug;
        
        clearTimeout(slugCheckTimeout);
        slugCheckTimeout = setTimeout(() => checkSlug(slug), 300);
    });

    // Initialize Quill editor
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#content-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            },
            placeholder: 'Enter page content...'
        });

        // Update hidden input before form submit
        document.getElementById('page-form').addEventListener('submit', function() {
            document.getElementById('content-input').value = quill.root.innerHTML;
        });
    });
</script>
@endpush
