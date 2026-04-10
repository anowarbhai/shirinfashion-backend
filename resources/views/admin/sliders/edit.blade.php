@extends('admin.layouts.master')

@section('title', 'Edit Slider - Shirin Fashion Admin')
@section('header', 'Edit Slider')

@section('content')
<div class="max-w-5xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Edit Slider</h2>
            <p class="text-gray-600 text-sm mt-1">Update hero banner slider details</p>
        </div>

        <form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column - Image & Basic Info -->
                <div class="space-y-6">
                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Slider Image
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-rose-500 transition cursor-pointer" onclick="document.getElementById('image').click()">
                            <div id="image-preview" class="mb-4">
                                <img id="preview-img" src="{{ $slider->image_url }}" alt="Current" class="max-h-48 mx-auto rounded-lg">
                            </div>
                            <div id="upload-placeholder" class="hidden">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600">Click to upload new image</p>
                                <p class="text-gray-400 text-sm mt-1">PNG, JPG, GIF, WEBP up to 5MB</p>
                            </div>
                            <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content Position -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content Position</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="content_position" value="top" {{ old('content_position', $slider->content_position) == 'top' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-3 py-2 text-center text-sm rounded-lg border-2 border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition">
                                    <i class="fas fa-arrow-up block mb-1"></i>
                                    Top
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="content_position" value="center" {{ old('content_position', $slider->content_position) == 'center' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-3 py-2 text-center text-sm rounded-lg border-2 border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition">
                                    <i class="fas fa-arrows-alt-v block mb-1"></i>
                                    Center
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="content_position" value="bottom" {{ old('content_position', $slider->content_position) == 'bottom' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-3 py-2 text-center text-sm rounded-lg border-2 border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition">
                                    <i class="fas fa-arrow-down block mb-1"></i>
                                    Bottom
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Text Alignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Text Alignment</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="text_align" value="left" {{ old('text_align', $slider->text_align) == 'left' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-3 py-2 text-center text-sm rounded-lg border-2 border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition">
                                    <i class="fas fa-align-left block mb-1"></i>
                                    Left
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="text_align" value="center" {{ old('text_align', $slider->text_align) == 'center' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-3 py-2 text-center text-sm rounded-lg border-2 border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition">
                                    <i class="fas fa-align-center block mb-1"></i>
                                    Center
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="text_align" value="right" {{ old('text_align', $slider->text_align) == 'right' ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-3 py-2 text-center text-sm rounded-lg border-2 border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition">
                                    <i class="fas fa-align-right block mb-1"></i>
                                    Right
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Order & Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                            <input type="number" name="order" value="{{ old('order', $slider->order) }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                            <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                        </div>
                        <div class="flex items-end pb-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ $slider->is_active ? 'checked' : '' }}
                                    class="w-4 h-4 text-rose-600 rounded focus:ring-rose-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Content & Buttons -->
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" value="{{ old('title', $slider->title) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            placeholder="e.g., Summer Collection 2024">
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                        <textarea name="subtitle" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 resize-none"
                            placeholder="e.g., Discover the latest trends with up to 50% off">{{ old('subtitle', $slider->subtitle) }}</textarea>
                    </div>

                    <!-- Button 1 Settings -->
                    <div class="border-t pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                            <span class="w-6 h-6 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-xs mr-2">1</span>
                            Primary Button
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                                    <input type="text" name="button_text" value="{{ old('button_text', $slider->button_text) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                                        placeholder="e.g., Shop Now">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Color</label>
                                    <select name="button_color" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                                        <option value="rose" {{ old('button_color', $slider->button_color) == 'rose' ? 'selected' : '' }}>Rose</option>
                                        <option value="blue" {{ old('button_color', $slider->button_color) == 'blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="green" {{ old('button_color', $slider->button_color) == 'green' ? 'selected' : '' }}>Green</option>
                                        <option value="purple" {{ old('button_color', $slider->button_color) == 'purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="orange" {{ old('button_color', $slider->button_color) == 'orange' ? 'selected' : '' }}>Orange</option>
                                        <option value="dark" {{ old('button_color', $slider->button_color) == 'dark' ? 'selected' : '' }}>Dark</option>
                                        <option value="white" {{ old('button_color', $slider->button_color) == 'white' ? 'selected' : '' }}>White</option>
                                        <option value="outline" {{ old('button_color', $slider->button_color) == 'outline' ? 'selected' : '' }}>Outline</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Button Link</label>
                                <input type="text" name="button_link" value="{{ old('button_link', $slider->button_link) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                                    placeholder="e.g., /shop or https://example.com">
                            </div>
                        </div>
                    </div>

                    <!-- Button 2 Settings -->
                    <div class="border-t pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-4 flex items-center">
                            <span class="w-6 h-6 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center text-xs mr-2">2</span>
                            Secondary Button (Optional)
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                                    <input type="text" name="button_2_text" value="{{ old('button_2_text', $slider->button_2_text) }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                                        placeholder="e.g., Learn More">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Button Color</label>
                                    <select name="button_2_color" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                                        <option value="outline" {{ old('button_2_color', $slider->button_2_color) == 'outline' ? 'selected' : '' }}>Outline</option>
                                        <option value="rose" {{ old('button_2_color', $slider->button_2_color) == 'rose' ? 'selected' : '' }}>Rose</option>
                                        <option value="blue" {{ old('button_2_color', $slider->button_2_color) == 'blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="green" {{ old('button_2_color', $slider->button_2_color) == 'green' ? 'selected' : '' }}>Green</option>
                                        <option value="purple" {{ old('button_2_color', $slider->button_2_color) == 'purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="orange" {{ old('button_2_color', $slider->button_2_color) == 'orange' ? 'selected' : '' }}>Orange</option>
                                        <option value="dark" {{ old('button_2_color', $slider->button_2_color) == 'dark' ? 'selected' : '' }}>Dark</option>
                                        <option value="white" {{ old('button_2_color', $slider->button_2_color) == 'white' ? 'selected' : '' }}>White</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Button Link</label>
                                <input type="text" name="button_2_link" value="{{ old('button_2_link', $slider->button_2_link) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                                    placeholder="e.g., /about or https://example.com">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t">
                <a href="{{ route('admin.sliders.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Update Slider
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').classList.remove('hidden');
            document.getElementById('upload-placeholder').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
