<!-- Media Selector Modal -->
<div id="media-selector-modal" class="fixed inset-0 bg-black/50 z-[60] hidden">
    <div class="absolute inset-4 md:inset-10 bg-white rounded-xl shadow-2xl flex flex-col overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Media Library</h3>
            <div class="flex items-center gap-3">
                <button type="button" onclick="openMediaUpload()" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 text-sm">
                    <i class="fas fa-upload mr-1"></i> Upload
                </button>
                <button type="button" onclick="closeMediaSelector()" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
        </div>
        
        <!-- Body -->
        <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
            <!-- Sidebar -->
            <div class="w-full md:w-64 border-b md:border-b-0 md:border-r p-4">
                <div class="mb-4">
                    <input type="search" id="media-search" placeholder="Search images..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-rose-500">
                </div>
                <div class="mb-4">
                    <select id="media-month-filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">All Dates</option>
                    </select>
                </div>
                <button type="button" onclick="clearMediaSelection()" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">
                    <i class="fas fa-refresh mr-2"></i> Clear Selection
                </button>
            </div>
            
            <!-- Media Grid -->
            <div class="flex-1 p-4 overflow-y-auto">
                <!-- Upload Area (Hidden by default) -->
                <div id="media-upload-area" class="hidden mb-4 p-8 border-2 border-dashed border-gray-300 rounded-lg text-center">
                    <input type="file" id="media-file-input" multiple accept="image/*" class="hidden">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-gray-600 mb-2">Drag and drop files here or</p>
                    <button type="button" onclick="document.getElementById('media-file-input').click()" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 text-sm">
                        Browse Files
                    </button>
                </div>
                
                <div id="media-grid-container" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                    <!-- Images will be loaded here -->
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <i class="fas fa-images text-4xl mb-4"></i>
                        <p>Loading media...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="flex items-center justify-between px-6 py-4 border-t bg-gray-50">
            <div id="media-selection-info" class="text-sm text-gray-600">
                Select an image to use
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeMediaSelector()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="media-select-btn" onclick="confirmMediaSelection()" class="px-6 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600" disabled>
                    Select
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Progress -->
<div id="media-upload-progress" class="hidden fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 z-[70]">
    <div class="flex items-center gap-3">
        <i class="fas fa-spinner fa-spin text-rose-500"></i>
        <span id="media-upload-status">Uploading...</span>
    </div>
</div>

<script>
// Media Selector Component - Global functions
(function() {
    if (window.mediaSelectorInitialized) return;
    window.mediaSelectorInitialized = true;
})();

let mediaCallback = null;
let selectedMediaUrls = [];
let currentMediaType = 'single';
let mediaCache = [];

function openMediaSelector(callback, type = 'single') {
    mediaCallback = callback;
    currentMediaType = type;
    selectedMediaUrls = [];
    
    document.getElementById('media-selector-modal').classList.remove('hidden');
    updateSelectionInfo();
    
    loadMediaLibrary();
    attachFileInputHandler();
}

function closeMediaSelector() {
    document.getElementById('media-selector-modal').classList.add('hidden');
    mediaCallback = null;
    selectedMediaUrls = [];
}

function openMediaUpload() {
    const uploadArea = document.getElementById('media-upload-area');
    uploadArea.classList.toggle('hidden');
}

function attachFileInputHandler() {
    const fileInput = document.getElementById('media-file-input');
    if (fileInput && !fileInput.dataset.attached) {
        fileInput.dataset.attached = 'true';
        fileInput.addEventListener('change', handleFileSelect);
    }
}

async function handleFileSelect(e) {
    const files = Array.from(e.target.files);
    if (files.length === 0) return;
    
    const progress = document.getElementById('media-upload-progress');
    const status = document.getElementById('media-upload-status');
    progress.classList.remove('hidden');
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        status.textContent = `Uploading ${i + 1} of ${files.length}...`;
        
        const formData = new FormData();
        formData.append('file', file);
        
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                alert('CSRF token not found');
                continue;
            }
            
            const response = await fetch('/admin/media/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });
            
            const data = await response.json();
            console.log('Upload response:', data);
            
            if (data.success) {
                mediaCache.unshift(data.media);
            } else {
                alert(data.message || 'Upload failed');
            }
        } catch (err) {
            console.error('Upload failed:', err);
            alert('Upload failed: ' + err.message);
        }
    }
    
    progress.classList.add('hidden');
    renderMediaGrid(mediaCache);
    document.getElementById('media-file-input').value = '';
    document.getElementById('media-upload-area').classList.add('hidden');
}

function loadMediaLibrary(search = '', month = '') {
    const container = document.getElementById('media-grid-container');
    container.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500"><i class="fas fa-spinner fa-spin text-2xl"></i><p class="mt-2">Loading...</p></div>';
    
    let url = '/admin/media?per_page=100';
    if (search) url += '&search=' + encodeURIComponent(search);
    if (month) url += '&month=' + encodeURIComponent(month);
    
    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log('Media response:', data);
            // Handle Laravel paginate response - items are in data.data
            let items = [];
            if (Array.isArray(data)) {
                items = data;
            } else if (data.data) {
                items = Array.isArray(data.data) ? data.data : data.data.data || [];
            }
            mediaCache = items;
            console.log('Items to render:', items);
            renderMediaGrid(mediaCache);
            loadMediaFilters();
        })
        .catch(err => {
            container.innerHTML = '<div class="col-span-full text-center py-12 text-red-500"><i class="fas fa-exclamation-circle text-2xl"></i><p class="mt-2">Failed to load media: ' + err.message + '</p></div>';
        });
}

function renderMediaGrid(images) {
    const container = document.getElementById('media-grid-container');
    
    if (!images || images.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500"><i class="fas fa-images text-4xl mb-4"></i><p>No images found</p></div>';
        return;
    }
    
    const baseUrl = window.location.origin;
    
    container.innerHTML = images.map(img => {
        // Use file_path for the actual value, url for display
        const filePath = img.file_path || img.url || '';
        // Handle paths with or without leading slash
        let displayUrl = img.url || '';
        if (!displayUrl.startsWith('http') && filePath) {
            displayUrl = baseUrl + (filePath.startsWith('/') ? filePath : '/' + filePath);
        }
        
        return `
        <div class="media-select-item relative bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-rose-300 transition-all"
             data-url="${filePath}" data-id="${img.id}">
            <img src="${displayUrl}" alt="${img.alt_text || img.name || 'Image'}" class="w-full aspect-square object-cover">
            <div class="absolute inset-0 bg-black/0 hover:bg-black/20 transition-all"></div>
        </div>
    `}).join('');
    
    // Add click handlers
    container.querySelectorAll('.media-select-item').forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMediaSelection(item);
        });
    });
}

function toggleMediaSelection(item) {
    const url = item.dataset.url;
    
    if (selectedMediaUrls.includes(url)) {
        selectedMediaUrls = selectedMediaUrls.filter(u => u !== url);
        item.classList.remove('border-rose-500');
        item.classList.add('border-transparent');
        const checkDiv = item.querySelector('.bg-rose-500');
        if (checkDiv) checkDiv.remove();
    } else {
        selectedMediaUrls.push(url);
        item.classList.add('border-rose-500');
        item.classList.remove('border-transparent');
        item.querySelector('.absolute').insertAdjacentHTML('afterend', '<div class="absolute top-1 right-1 z-10 w-6 h-6 bg-rose-500 rounded-full flex items-center justify-center"><i class="fas fa-check text-white text-xs"></i></div>');
    }
    
    updateSelectionInfo();
}

function updateSelectionInfo() {
    const info = document.getElementById('media-selection-info');
    const selectBtn = document.getElementById('media-select-btn');
    
    if (currentMediaType === 'gallery') {
        if (selectedMediaUrls.length === 0) {
            info.textContent = 'Click images to select multiple images';
            selectBtn.disabled = selectedMediaUrls.length === 0;
            selectBtn.textContent = 'Add to Gallery';
        } else {
            info.textContent = `${selectedMediaUrls.length} image${selectedMediaUrls.length > 1 ? 's' : ''} selected`;
            selectBtn.disabled = selectedMediaUrls.length === 0;
            selectBtn.textContent = `Add ${selectedMediaUrls.length} Image${selectedMediaUrls.length > 1 ? 's' : ''} to Gallery`;
        }
    } else {
        info.textContent = selectedMediaUrls.length > 0 ? `Selected: ${selectedMediaUrls[0].split('/').pop()}` : 'Select an image to use';
        selectBtn.disabled = selectedMediaUrls.length === 0;
        selectBtn.textContent = 'Select';
    }
}

function confirmMediaSelection() {
    if (currentMediaType === 'gallery') {
        if (selectedMediaUrls.length > 0) {
            // Get current URLs from hidden input
            const currentUrls = document.getElementById('images-input').value;
            const urls = currentUrls ? JSON.parse(currentUrls) : [];
            const baseUrl = window.location.origin;
            
            // Filter out URLs that already exist
            const newUrls = selectedMediaUrls.filter(url => {
                // Check if URL already exists (handle both relative and absolute)
                const exists = urls.some(existingUrl => {
                    return existingUrl === url || 
                           existingUrl.endsWith(url) || 
                           url.endsWith(existingUrl);
                });
                return !exists;
            });
            
            // Only add if there are new URLs
            if (newUrls.length > 0) {
                const allUrls = [...urls, ...newUrls];
                document.getElementById('images-input').value = JSON.stringify(allUrls);
                
                // Add preview for new images only
                const preview = document.getElementById('gallery-preview');
                newUrls.forEach(url => {
                    // Full image URL for display - handle paths with or without leading slash
                    const imgSrc = url.startsWith('http') ? url : baseUrl + (url.startsWith('/') ? url : '/' + url);
                    preview.insertAdjacentHTML('beforeend', `
                        <div class="relative group" data-url="${url}">
                            <img src="${imgSrc}" class="w-20 h-20 object-cover rounded">
                            <button type="button" onclick="removeGalleryImage(this, '${url.replace(/'/g, "\\'")}')" class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity -mt-1 -mr-1">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    `);
                });
            }
            
            // Close modal
            closeMediaSelector();
        } else {
            closeMediaSelector();
        }
    } else {
        // For single image, call callback with URL
        if (selectedMediaUrls.length > 0 && mediaCallback) {
            mediaCallback(selectedMediaUrls[0]);
            closeMediaSelector();
        }
    }
}

function clearMediaSelection() {
    selectedMediaUrls = [];
    document.querySelectorAll('.media-select-item').forEach(el => {
        el.classList.remove('border-rose-500');
        el.classList.add('border-transparent');
        const checkDiv = el.querySelector('.bg-rose-500');
        if (checkDiv) checkDiv.remove();
    });
    updateSelectionInfo();
}

function loadMediaFilters() {
    // Load month filter options
    fetch('/admin/media?per_page=1')
        .then(res => res.json())
        .then(data => {
            // We need to get months from a separate API or include in the main response
            // For now, leave empty
        });
}

// Search handler
document.getElementById('media-search')?.addEventListener('input', (e) => {
    loadMediaLibrary(e.target.value, document.getElementById('media-month-filter').value);
});

document.getElementById('media-month-filter')?.addEventListener('change', (e) => {
    loadMediaLibrary(document.getElementById('media-search').value, e.target.value);
});

// File upload handler - now attached when modal opens

// Example usage in forms:
// To select single image: openMediaSelector((url) => { document.getElementById('image-input').value = url; document.getElementById('thumbnail-preview').querySelector('img').src = '/' + url; }, 'single');
// To select gallery images: openMediaSelector(null, 'gallery');
</script>
