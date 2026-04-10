@extends('admin.layouts.master')

@section('title', 'Media Library - Shirin Fashion Admin')
@section('header', 'Media Library')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <!-- Upload Area -->
    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer" id="upload-area">
        <input type="file" id="file-input" multiple accept="image/*" class="hidden">
        <input type="file" id="single-file-input" accept="image/*" class="hidden">
        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <p class="text-gray-600 mt-4 mb-4">Drag and drop images here or click to upload</p>
        <div class="flex justify-center gap-3">
            <button type="button" class="px-5 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700" onclick="event.stopPropagation(); document.getElementById('single-file-input').click()">
                <i class="fas fa-upload mr-2"></i> Upload
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-4">JPEG, PNG, JPG, GIF, WebP (Max 5MB)</p>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-wrap gap-3 items-center mt-6 mb-4 justify-between">
        <form method="GET" action="{{ route('admin.media.index') }}" class="flex gap-3 flex-wrap items-center">
            <input type="search" name="search" placeholder="Search media..." value="{{ request('search') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            <select name="month" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                <option value="">All Dates</option>
                @foreach($months as $month)
                    <option value="{{ $month['value'] }}" {{ request('month') == $month['value'] ? 'selected' : '' }}>
                        {{ $month['label'] }} ({{ $month['count'] }})
                    </option>
                @endforeach
            </select>
            @if(request('search') || request('month'))
                <a href="{{ route('admin.media.index') }}" class="px-4 py-2 text-rose-500 hover:text-rose-600">
                    <i class="fas fa-times"></i> Clear
                </a>
            @endif
        </form>
        
        <!-- Bulk Actions -->
        <div id="bulk-actions" class="hidden items-center gap-3">
            <span id="selected-count" class="text-sm text-gray-600">0 selected</span>
            <button type="button" onclick="deleteSelected()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                <i class="fas fa-trash mr-1"></i> Delete Selected
            </button>
            <button type="button" onclick="clearSelection()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                Cancel
            </button>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="media-grid">
        @forelse($media as $item)
            <div class="media-item group relative bg-gray-50 rounded-lg overflow-hidden border border-gray-200 hover:border-rose-300 transition-all" data-id="{{ $item->id }}" data-url="{{ $item->url }}">
                <div class="absolute top-2 left-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                    <input type="checkbox" class="media-checkbox w-5 h-5 rounded border-gray-300 text-rose-500 focus:ring-rose-500 cursor-pointer">
                </div>
                <div class="relative aspect-square cursor-pointer">
                    <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->name }}" loading="lazy" class="w-full h-full object-cover">
                </div>
                <div class="absolute bottom-2 right-2">
                    <button type="button" class="delete-btn w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center justify-center" onclick="var btn=this; var id={{ $item->id }}; if(confirm('Delete this image?')) { fetch('/admin/media/'+id, {method:'DELETE', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').getAttribute('content')}}).then(function(r){ return r.json(); }).then(function(d){ if(d.success) { btn.closest('.media-item').remove(); alert('Deleted!'); } else { alert('Delete failed!'); } }).catch(function(e){ alert('Error: '+e); }); }">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
                <div class="p-2 text-center">
                    <p class="text-xs text-gray-700 truncate" title="{{ $item->name }}">{{ $item->name }}</p>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <i class="fas fa-images text-4xl mb-4"></i>
                <p>No media found. Upload some images to get started.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $media->appends(request()->query())->links() }}
    </div>
</div>

<!-- Upload Progress Modal -->
<div id="upload-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4">Uploading...</h3>
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
            <div id="upload-progress" class="bg-rose-500 h-2.5 rounded-full transition-all" style="width: 0%"></div>
        </div>
        <p id="upload-status" class="text-sm text-gray-600">Preparing...</p>
    </div>
</div>
@endsection

@section('js')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('file-input');
    const singleFileInput = document.getElementById('single-file-input');
    const mediaGrid = document.getElementById('media-grid');
    const uploadModal = document.getElementById('upload-modal');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    let selectedIds = new Set();

    // Click to upload area
    uploadArea.addEventListener('click', () => singleFileInput.click());

    // Drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.add('border-rose-400', 'bg-rose-50'));
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => uploadArea.classList.remove('border-rose-400', 'bg-rose-50'));
    });

    uploadArea.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) uploadFiles(files);
    });

    // File input changes
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) uploadFiles(e.target.files);
    });

    singleFileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) uploadFiles(e.target.files);
    });

    async function uploadFiles(files) {
        uploadModal.classList.remove('hidden');
        uploadModal.classList.add('flex');
        
        const total = files.length;
        let uploaded = 0;

        for (let file of files) {
            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('{{ route('admin.media.upload') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    uploaded++;
                    document.getElementById('upload-progress').style.width = Math.round((uploaded / total) * 100) + '%';
                    document.getElementById('upload-status').textContent = `Uploading ${uploaded} of ${total}...`;
                    
                    const item = createMediaItem(data.media);
                    mediaGrid.insertAdjacentHTML('afterbegin', item);
                }
            } catch (error) {
                console.error('Upload failed:', error);
            }
        }

        document.getElementById('upload-status').textContent = 'Upload complete!';
        setTimeout(() => {
            uploadModal.classList.add('hidden');
            uploadModal.classList.remove('flex');
            document.getElementById('upload-progress').style.width = '0%';
            window.location.reload();
        }, 1000);
    }

    function createMediaItem(media) {
        return `
            <div class="media-item group relative bg-gray-50 rounded-lg overflow-hidden border border-gray-200 hover:border-rose-300 transition-all" data-id="${media.id}" data-url="${media.file_path}">
                <div class="absolute top-2 left-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                    <input type="checkbox" class="media-checkbox w-5 h-5 rounded border-gray-300 text-rose-500 focus:ring-rose-500 cursor-pointer">
                </div>
                <div class="relative aspect-square cursor-pointer">
                    <img src="${media.url}" alt="${media.alt_text || media.name}" loading="lazy" class="w-full h-full object-cover">
                </div>
                <div class="absolute bottom-2 right-2">
                    <button type="button" class="delete-btn w-8 h-8 bg-red-500 text-white rounded-lg hover:bg-red-600 flex items-center justify-center" onclick="var btn=this; if(confirm('Delete this image?')) { fetch('/admin/media/'+${media.id}, {method:'DELETE', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').getAttribute('content')}}).then(r=>r.json()).then(d=>{ if(d.success) btn.closest('.media-item').remove(); alert('Deleted!'); }); }">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
                <div class="p-2 text-center">
                    <p class="text-xs text-gray-700 truncate" title="${media.name}">${media.name}</p>
                </div>
            </div>
        `;
    }

    function toggleSelect(item, event) {
        const checkbox = item.querySelector('input[type="checkbox"]');
        const id = item.dataset.id;
        
        if (checkbox.checked) {
            checkbox.checked = false;
            item.classList.remove('ring-2', 'ring-rose-500', 'border-rose-500');
            selectedIds.delete(id);
        } else {
            checkbox.checked = true;
            item.classList.add('ring-2', 'ring-rose-500', 'border-rose-500');
            selectedIds.add(id);
        }
        
        updateBulkActions();
    }

    function updateBulkActions() {
        if (selectedIds.size > 0) {
            bulkActions.classList.remove('hidden');
            bulkActions.classList.add('flex');
            selectedCount.textContent = selectedIds.size + ' selected';
        } else {
            bulkActions.classList.add('hidden');
            bulkActions.classList.remove('flex');
        }
    }

    function clearSelection() {
        selectedIds.clear();
        document.querySelectorAll('.media-item input[type="checkbox"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('.media-item').forEach(item => {
            item.classList.remove('ring-2', 'ring-rose-500', 'border-rose-500');
        });
        updateBulkActions();
    }

    async function deleteSelected() {
        if (selectedIds.size === 0) return;
        
        if (!confirm(`Delete ${selectedIds.size} selected image(s)?`)) return;

        const ids = Array.from(selectedIds);
        let deleted = 0;

        for (const id of ids) {
            try {
                const response = await fetch(`/admin/media/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    document.querySelector(`.media-item[data-id="${id}"]`)?.remove();
                    deleted++;
                }
            } catch (error) {
                console.error('Delete failed:', error);
            }
        }

        selectedIds.clear();
        updateBulkActions();
        alert(`${deleted} image(s) deleted`);
    }

    async function deleteMedia(id) {
        if (!confirm('Delete this image?')) return;

        try {
            const response = await fetch(`/admin/media/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });

            const data = await response.json();
            if (data.success) {
                document.querySelector(`.media-item[data-id="${id}"]`)?.remove();
                selectedIds.delete(String(id));
                updateBulkActions();
            }
        } catch (error) {
            console.error('Delete failed:', error);
        }
    }

    // Event delegation for delete buttons
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            const id = deleteBtn.getAttribute('data-delete-id');
            if (id) {
                deleteMedia(parseInt(id));
            }
            return;
        }
        
        // Handle checkbox click
        const checkbox = e.target.closest('.media-checkbox');
        if (checkbox) {
            e.stopPropagation();
            const item = checkbox.closest('.media-item');
            if (item) {
                toggleSelect(item);
            }
            return;
        }
        
        // Handle item click
        const item = e.target.closest('.media-item');
        if (item) {
            toggleSelect(item);
        }
    });
</script>
@endsection
