@extends('admin.layouts.master')

@section('title', 'Appearance - Shirin Fashion Admin')
@section('header', 'Appearance Settings')

@section('content')

<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.themes.appearance.update') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Logo Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Logo <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-500 ml-1">(Recommended: 200x60px)</span>
                </label>
                <div class="space-y-3">
                    <!-- File Upload -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-rose-500 transition-colors">
                        @if($settings->logo)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $settings->logo) }}" alt="Current Logo" class="h-16 mx-auto">
                        </div>
                        @endif
                        <input type="file" name="logo" id="logo" accept="image/*" class="hidden" onchange="previewImage(this, 'logo-preview')">
                        <label for="logo" class="cursor-pointer text-rose-600 hover:text-rose-700">
                            <i class="fas fa-cloud-upload-alt text-2xl mb-2"></i>
                            <p>Click to upload logo</p>
                        </label>
                        <div id="logo-preview" class="mt-4 hidden">
                            <img src="" alt="Logo Preview" class="h-16 mx-auto">
                        </div>
                    </div>
                    
                    <!-- Media Library Button -->
                    <button type="button" onclick="openMediaSelector((url) => { document.getElementById('logo-input').value = url; setLogoPreview(url); }, 'single')" class="w-full px-4 py-2 border border-rose-300 text-rose-600 rounded-lg hover:bg-rose-50 transition-colors">
                        <i class="fas fa-image mr-2"></i>Or select from Media Library
                    </button>
                    <input type="hidden" id="logo-input" name="logo_path">
                </div>
                @error('logo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Favicon Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Favicon <span class="text-red-500">*</span>
                    <span class="text-xs text-gray-500 ml-1">(Recommended: 32x32px, .ico or .png)</span>
                </label>
                <div class="space-y-3">
                    <!-- File Upload -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-rose-500 transition-colors">
                        @if($settings->favicon)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Current Favicon" class="h-8 w-8 mx-auto">
                        </div>
                        @endif
                        <input type="file" name="favicon" id="favicon" accept=".ico,.png" class="hidden" onchange="previewImage(this, 'favicon-preview')">
                        <label for="favicon" class="cursor-pointer text-rose-600 hover:text-rose-700">
                            <i class="fas fa-cloud-upload-alt text-2xl mb-2"></i>
                            <p>Click to upload favicon</p>
                        </label>
                        <div id="favicon-preview" class="mt-4 hidden">
                            <img src="" alt="Favicon Preview" class="h-8 w-8 mx-auto">
                        </div>
                    </div>
                    
                    <!-- Media Library Button -->
                    <button type="button" onclick="openMediaSelector((url) => { document.getElementById('favicon-input').value = url; setFaviconPreview(url); }, 'single')" class="w-full px-4 py-2 border border-rose-300 text-rose-600 rounded-lg hover:bg-rose-50 transition-colors">
                        <i class="fas fa-image mr-2"></i>Or select from Media Library
                    </button>
                    <input type="hidden" id="favicon-input" name="favicon_path">
                </div>
                @error('favicon')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Footer Logo Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Footer Logo
                    <span class="text-xs text-gray-500 ml-1">(Optional, Recommended: 150x50px)</span>
                </label>
                <div class="space-y-3">
                    <!-- File Upload -->
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-rose-500 transition-colors">
                        @if($settings->footer_logo)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $settings->footer_logo) }}" alt="Current Footer Logo" class="h-12 mx-auto">
                        </div>
                        @endif
                        <input type="file" name="footer_logo" id="footer_logo" accept="image/*" class="hidden" onchange="previewImage(this, 'footer-logo-preview')">
                        <label for="footer_logo" class="cursor-pointer text-rose-600 hover:text-rose-700">
                            <i class="fas fa-cloud-upload-alt text-2xl mb-2"></i>
                            <p>Click to upload footer logo</p>
                        </label>
                        <div id="footer-logo-preview" class="mt-4 hidden">
                            <img src="" alt="Footer Logo Preview" class="h-12 mx-auto">
                        </div>
                    </div>
                    
                    <!-- Media Library Button -->
                    <button type="button" onclick="openMediaSelector((url) => { document.getElementById('footer-logo-input').value = url; setFooterLogoPreview(url); }, 'single')" class="w-full px-4 py-2 border border-rose-300 text-rose-600 rounded-lg hover:bg-rose-50 transition-colors">
                        <i class="fas fa-image mr-2"></i>Or select from Media Library
                    </button>
                    <input type="hidden" id="footer-logo-input" name="footer_logo_path">
                </div>
                @error('footer_logo')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Company Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Company Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="company_name" value="{{ old('company_name', $settings->company_name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                @error('company_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tagline -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tagline
                    <span class="text-xs text-gray-500 ml-1">(Optional)</span>
                </label>
                <input type="text" name="tagline" value="{{ old('tagline', $settings->tagline) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="e.g., Premium Fashion Store">
            </div>

            <!-- Company Details -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Company Details
                    <span class="text-xs text-gray-500 ml-1">(Optional)</span>
                </label>
                <textarea name="company_details" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="Enter company description, business hours, or any other details...">{{ old('company_details', $settings->company_details) }}</textarea>
            </div>
        </div>

        <!-- Contact Information Section -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-address-card mr-2 text-rose-600"></i>Contact Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-1 text-gray-400"></i>Email Address
                    </label>
                    <input type="email" name="email" value="{{ old('email', $settings->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="contact@example.com">
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-phone mr-1 text-gray-400"></i>Phone Number
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $settings->phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="+880 1XXX-XXXXXX">
                    @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>Address
                    </label>
                    <textarea name="address" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="Enter your full address...">{{ old('address', $settings->address) }}</textarea>
                    @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Social Media Section -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                <i class="fas fa-share-alt mr-2 text-rose-600"></i>Social Media Links
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Facebook -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-facebook mr-1 text-blue-600"></i>Facebook
                    </label>
                    <input type="url" name="facebook" value="{{ old('facebook', $settings->facebook) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="https://facebook.com/yourpage">
                    @error('facebook')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Instagram -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-instagram mr-1 text-pink-600"></i>Instagram
                    </label>
                    <input type="url" name="instagram" value="{{ old('instagram', $settings->instagram) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="https://instagram.com/yourhandle">
                    @error('instagram')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Twitter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-twitter mr-1 text-blue-400"></i>Twitter
                    </label>
                    <input type="url" name="twitter" value="{{ old('twitter', $settings->twitter) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="https://twitter.com/yourhandle">
                    @error('twitter')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- YouTube -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-youtube mr-1 text-red-600"></i>YouTube
                    </label>
                    <input type="url" name="youtube" value="{{ old('youtube', $settings->youtube) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="https://youtube.com/yourchannel">
                    @error('youtube')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- LinkedIn -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-linkedin mr-1 text-blue-700"></i>LinkedIn
                    </label>
                    <input type="url" name="linkedin" value="{{ old('linkedin', $settings->linkedin) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="https://linkedin.com/company/yourcompany">
                    @error('linkedin')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- TikTok -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-tiktok mr-1 text-black"></i>TikTok
                    </label>
                    <input type="url" name="tiktok" value="{{ old('tiktok', $settings->tiktok) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="https://tiktok.com/@yourhandle">
                    @error('tiktok')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t">
            <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
                <i class="fas fa-save mr-2"></i>Save Appearance Settings
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function setLogoPreview(url) {
    const preview = document.getElementById('logo-preview');
    const img = preview.querySelector('img');
    const baseUrl = window.location.origin;
    
    // Handle URL - add domain if it's a relative path
    const fullUrl = url.startsWith('http') ? url : baseUrl + (url.startsWith('/') ? url : '/' + url);
    
    img.src = fullUrl;
    preview.classList.remove('hidden');
}

function setFaviconPreview(url) {
     const preview = document.getElementById('favicon-preview');
     const img = preview.querySelector('img');
     const baseUrl = window.location.origin;
     
     // Handle URL - add domain if it's a relative path
     const fullUrl = url.startsWith('http') ? url : baseUrl + (url.startsWith('/') ? url : '/' + url);
     
     img.src = fullUrl;
     preview.classList.remove('hidden');
 }

 function setFooterLogoPreview(url) {
     const preview = document.getElementById('footer-logo-preview');
     const img = preview.querySelector('img');
     const baseUrl = window.location.origin;
     
     // Handle URL - add domain if it's a relative path
     const fullUrl = url.startsWith('http') ? url : baseUrl + (url.startsWith('/') ? url : '/' + url);
     
     img.src = fullUrl;
     preview.classList.remove('hidden');
 }

 // Handle form submission to use media library URLs if selected
 document.querySelector('form').addEventListener('submit', function(e) {
     const logoUrl = document.getElementById('logo-input').value;
     const faviconUrl = document.getElementById('favicon-input').value;
     const footerLogoUrl = document.getElementById('footer-logo-input').value;
     
     // If media library URL is selected, set it as file input
     if (logoUrl && !document.getElementById('logo').files.length) {
         // Create a hidden input to store the URL
         let input = document.querySelector('input[name="logo_path"]');
         if (!input) {
             input = document.createElement('input');
             input.type = 'hidden';
             input.name = 'logo_path';
             this.appendChild(input);
         }
         input.value = logoUrl;
     }
     
     if (faviconUrl && !document.getElementById('favicon').files.length) {
         // Create a hidden input to store the URL
         let input = document.querySelector('input[name="favicon_path"]');
         if (!input) {
             input = document.createElement('input');
             input.type = 'hidden';
             input.name = 'favicon_path';
             this.appendChild(input);
         }
         input.value = faviconUrl;
     }

     if (footerLogoUrl && !document.getElementById('footer_logo').files.length) {
         // Create a hidden input to store the URL
         let input = document.querySelector('input[name="footer_logo_path"]');
         if (!input) {
             input = document.createElement('input');
             input.type = 'hidden';
             input.name = 'footer_logo_path';
             this.appendChild(input);
         }
         input.value = footerLogoUrl;
     }
 });
</script>
@include('admin.components.media-selector')
@endsection
