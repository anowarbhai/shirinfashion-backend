@extends('admin.layouts.builder')

@section('title', 'Page Builder - ' . $page->title)

@push('styles')
<style>
    .widget-drag-item {
        cursor: grab;
        transition: all 0.2s;
        user-select: none;
    }
    .widget-drag-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .widget-drag-item:active {
        cursor: grabbing;
    }
    .canvas-widget {
        transition: all 0.2s;
    }
    #canvas.drag-over {
        outline: 3px dashed #f43f5e;
        outline-offset: -3px;
        background-color: rgba(244, 63, 94, 0.05);
    }
    .canvas-widget:hover {
        outline: 2px dashed #f43f5e;
    }
    .canvas-widget.selected {
        outline: 2px solid #f43f5e;
    }
    .ghost {
        opacity: 0.5;
        background: #fecdd3;
    }
    .drag-over {
        background: #fecdd3;
    }
    .settings-panel {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    [draggable="true"] {
        user-select: none;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden">
    <!-- Widgets Panel -->
    <div class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Widgets</h3>
            <p class="text-sm text-gray-500">Click or drag widgets to the canvas</p>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="widget-list">
            @php
            $widgets = [
                ['id' => 'hero', 'name' => 'Hero Section', 'icon' => '🖼️'],
                ['id' => 'banner', 'name' => 'Banner', 'icon' => '🎨'],
                ['id' => 'title', 'name' => 'Title', 'icon' => '📌'],
                ['id' => 'text', 'name' => 'Text Block', 'icon' => '📝'],
                ['id' => 'product-grid', 'name' => 'Product Grid', 'icon' => '📦'],
                ['id' => 'product-carousel', 'name' => 'Product Carousel', 'icon' => '🎠'],
                ['id' => 'category-grid', 'name' => 'Category Grid', 'icon' => '📁'],
                ['id' => 'featured-products', 'name' => 'Featured Products', 'icon' => '⭐'],
                ['id' => 'new-arrivals', 'name' => 'New Arrivals', 'icon' => '🆕'],
                ['id' => 'newsletter', 'name' => 'Newsletter', 'icon' => '📧'],
                ['id' => 'faq', 'name' => 'FAQ', 'icon' => '❓'],
                ['id' => 'video', 'name' => 'Video', 'icon' => '🎬'],
                ['id' => 'spacer', 'name' => 'Spacer', 'icon' => '↕️'],
                ['id' => 'divider', 'name' => 'Divider', 'icon' => '➖'],
                ['id' => 'two-columns', 'name' => '2 Columns', 'icon' => '📊'],
                ['id' => 'three-columns', 'name' => '3 Columns', 'icon' => '📈'],
                ['id' => 'trust-badges', 'name' => 'Trust Badges', 'icon' => '✅'],
            ];
            @endphp
            @foreach($widgets as $widget)
            <div class="widget-drag-item bg-white border border-gray-200 rounded-lg p-3 flex items-center gap-3 shadow-sm cursor-pointer hover:border-rose-400 hover:shadow-md transition select-none"
                 draggable="true"
                 data-widget-type="{{ $widget['id'] }}"
                 ondragstart="console.log('Drag start:', this.dataset.widgetType); window.draggedWidget = this.dataset.widgetType; event.dataTransfer.setData('text/plain', this.dataset.widgetType); event.dataTransfer.effectAllowed = 'copy';"
                 ondragend="console.log('Drag end');">
                <span class="text-xl cursor-grab">{{ $widget['icon'] }}</span>
                <span class="text-sm font-medium text-gray-700 cursor-grab">{{ $widget['name'] }}</span>
                <button type="button" onclick="event.stopPropagation(); addWidget('{{ $widget['id'] }}');" class="ml-auto px-2 py-1 bg-rose-500 text-white text-xs rounded hover:bg-rose-600">+ Add</button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Canvas Area -->
    <div class="flex-1 bg-gray-100 flex flex-col overflow-hidden">
        @if(session('success'))
        <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                var el = document.getElementById('success-message');
                if (el) {
                    el.style.transition = 'opacity 0.5s';
                    el.style.opacity = '0';
                    setTimeout(function() { el.remove(); }, 500);
                }
            }, 2500);
        </script>
        @endif
        <!-- Top Bar -->
        <div class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.pages.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-semibold text-gray-800">{{ $page->title }}</h2>
                    <p class="text-sm text-gray-500">Page Builder</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="previewPage()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                    Preview
                </button>
                <button type="submit" form="widgets-form" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                    Save Changes
                </button>
            </div>
        </div>

        <!-- Canvas -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="max-w-5xl mx-auto bg-white min-h-[600px] rounded-lg shadow-sm p-8 relative" id="canvas">
                <!-- Loading Overlay -->
                <div id="canvas-loader" class="absolute inset-0 bg-white z-20 flex items-center justify-center rounded-lg">
                    <div class="text-center">
                        <svg class="animate-spin h-12 w-12 text-rose-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-gray-600 font-medium">Loading Page Builder...</p>
                        <p class="text-gray-400 text-sm mt-1">Please wait while widgets are loading</p>
                    </div>
                </div>
                
                @if($page->widgets && count($page->widgets) > 0)
                    @foreach($page->widgets as $index => $widget)
                    <div class="canvas-widget relative group mb-4 border-2 border-transparent rounded-lg cursor-pointer" 
                         data-widget-index="{{ $index }}"
                         data-widget-type="{{ $widget['id'] }}"
                         onclick="selectWidgetForEdit({{ $index }})">
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 flex gap-1 z-10">
                            <button type="button" onclick="event.stopPropagation(); editWidget({{ $index }})" class="p-1.5 bg-blue-500 text-white rounded hover:bg-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button type="button" onclick="event.stopPropagation(); moveWidget({{ $index }}, -1)" class="p-1.5 bg-gray-500 text-white rounded hover:bg-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button type="button" onclick="event.stopPropagation(); moveWidget({{ $index }}, 1)" class="p-1.5 bg-gray-500 text-white rounded hover:bg-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <button type="button" onclick="event.stopPropagation(); deleteWidget({{ $index }})" class="p-1.5 bg-red-500 text-white rounded hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        {!! renderWidgetPreview($widget['id'], $widget['settings'] ?? []) !!}
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-20 text-gray-400" id="empty-canvas">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="text-lg">Drag widgets here to build your page</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Settings Panel -->
    <div class="w-80 bg-white border-l border-gray-200 flex flex-col" id="settings-panel">
        <div class="p-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Widget Settings</h3>
            <p class="text-sm text-gray-500" id="selected-widget-name">Select a widget to edit</p>
        </div>
        <div class="flex-1 overflow-y-auto p-4 settings-panel" id="widget-settings">
            <p class="text-gray-400 text-center py-8">Click the edit button on a widget to change its settings</p>
        </div>
    </div>
        </div>

<!-- Hidden form for saving -->
<form id="widgets-form" method="POST" action="{{ route('admin.pages.builder-update', $page) }}" onsubmit="return saveWidgets();">
    @csrf
    <input type="hidden" name="widgets" id="widgets-input">
</form>

<script>
function saveWidgets() {
    if (!widgets || widgets.length === 0) {
        if (!confirm('No widgets to save. Are you sure you want to save?')) {
            return false;
        }
    }
    
    // Update input value with current widgets
    document.getElementById('widgets-input').value = JSON.stringify(widgets);
    
    // Disable button
    const saveBtn = document.querySelector('button[form="widgets-form"]');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }
    
    // Allow form to submit
    return true;
}
</script>

<!-- Preview Modal -->
<div class="fixed inset-0 bg-black/50 z-50 hidden" id="preview-modal">
    <div class="h-full flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 class="font-semibold">Preview</h3>
                <button onclick="closePreview()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]" id="preview-content">
            </div>
        </div>
    </div>
</div>

@php
function renderWidgetPreview($type, $settings) {
    $getSpacingStyle = function($s) {
        $pt = $s['padding_top'] ?? 20;
        $pb = $s['padding_bottom'] ?? 20;
        $mt = $s['margin_top'] ?? 0;
        $mb = $s['margin_bottom'] ?? 0;
        return 'padding-top: '.$pt.'px; padding-bottom: '.$pb.'px; margin-top: '.$mt.'px; margin-bottom: '.$mb.'px;';
    };
    
    $html = '';
    switch($type) {
        case 'hero':
            $title = $settings['title'] ?? 'Hero Title';
            $subtitle = $settings['subtitle'] ?? '';
            $bgType = $settings['bg_type'] ?? 'color';
            $bg = $settings['background'] ?? '#f3f4f6';
            $textColor = $settings['text_color'] ?? '#1f2937';
            $subtitleColor = $settings['subtitle_color'] ?? '#6b7280';
            $textAlign = $settings['text_align'] ?? 'center';
            $overlayOpacity = $settings['overlay_opacity'] ?? 0;
            $titleFontSize = $settings['title_font_size'] ?? '4xl';
            $subtitleFontSize = $settings['subtitle_font_size'] ?? 'xl';
            $buttonText = $settings['button_text'] ?? '';
            $buttonBg = $settings['button_bg'] ?? '#f43f5e';
            $buttonColor = $settings['button_color'] ?? '#ffffff';
            $spacingStyle = $getSpacingStyle($settings);
            
            $bgStyle = '';
            $hasOverlay = false;
            if ($bgType === 'gradient' && !empty($settings['gradient'])) {
                $bgStyle = 'background: '.$settings['gradient'].';';
                $hasOverlay = $overlayOpacity > 0;
            } elseif ($bgType === 'image' && !empty($settings['bg_image'])) {
                $bgStyle = 'background-image: url('.$settings['bg_image'].'); background-position: '.($settings['bg_position'] ?? 'center center').'; background-size: '.($settings['bg_size'] ?? 'cover').'; background-repeat: '.($settings['bg_repeat'] ?? 'no-repeat').';';
                $hasOverlay = $overlayOpacity > 0;
            } else {
                $bgStyle = 'background-color: '.$bg.';';
            }
            
            $titleClass = 'text-'.$titleFontSize;
            $subtitleClass = 'text-'.$subtitleFontSize;
            
            $html = '<div class="relative" style="'.$bgStyle.' '.$spacingStyle.'">';
            if ($hasOverlay) {
                $html .= '<div class="absolute inset-0 bg-black" style="opacity: '.($overlayOpacity / 100).';"></div>';
            }
            $html .= '<div class="relative z-10 max-w-4xl mx-auto" style="text-align: '.$textAlign.';">';
            $html .= '<h2 class="'.$titleClass.' font-bold mb-4" style="color: '.$textColor.';">'.e($title).'</h2>';
            if (!empty($subtitle)) {
                $html .= '<p class="'.$subtitleClass.' mb-4" style="color: '.$subtitleColor.';">'.e($subtitle).'</p>';
            }
            if (!empty($buttonText)) {
                $html .= '<button style="background-color: '.$buttonBg.'; color: '.$buttonColor.';" class="px-6 py-2 rounded-lg transition-colors">'.$buttonText.'</button>';
            }
            $html .= '</div></div>';
            break;
            
        case 'banner':
            $text = $settings['text'] ?? 'Banner Text';
            $bg = $settings['background'] ?? '#fce7f3';
            $textColor = $settings['text_color'] ?? '#831843';
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="text-center" style="background: '.$bg.'; '.$spacingStyle.';"><p class="text-2xl font-semibold" style="color: '.$textColor.';">'.e($text).'</p></div>';
            break;
            
        case 'title':
            $text = $settings['text'] ?? 'Your Title Here';
            $color = $settings['color'] ?? '#1f2937';
            $fontSize = $settings['font_size'] ?? '4xl';
            $fontWeight = $settings['font_weight'] ?? 'bold';
            $align = $settings['align'] ?? 'center';
            $marginBottom = $settings['margin_bottom'] ?? '16px';
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div style="'.$spacingStyle.' text-align: '.$align.';"><h2 class="text-'.$fontSize.' font-'.$fontWeight.'" style="color: '.$color.'; margin-bottom: '.$marginBottom.';">'.e($text).'</h2></div>';
            break;
            
        case 'text':
            $content = $settings['content'] ?? 'Text content goes here...';
            $textColor = $settings['text_color'] ?? '#374151';
            $fontSize = $settings['font_size'] ?? 'base';
            $textAlign = $settings['text_align'] ?? 'left';
            $spacingStyle = $getSpacingStyle($settings);
            
            $fontSizeMap = [
                'sm' => '0.875rem',
                'base' => '1rem',
                'lg' => '1.125rem',
                'xl' => '1.25rem'
            ];
            $fontSizeValue = $fontSizeMap[$fontSize] ?? '1rem';
            $fontSizeCss = $fontSizeValue;
            
            $html = '<div style="'.$spacingStyle.'"><div class="ql-editor-content" style="color: '.$textColor.'; text-align: '.$textAlign.';">';
            $html .= '<style>.ql-editor-content h1 { font-size: 2em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content h2 { font-size: 1.5em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content h3 { font-size: 1.25em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content h4 { font-size: 1em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content p, .ql-editor-content ul, .ql-editor-content ol { font-size: '.$fontSizeCss.'; line-height: 1.6; margin-bottom: 0.5em; }</style>';
            $html .= $content;
            $html .= '</div></div>';
            break;
            
        case 'product-grid':
            $limit = $settings['limit'] ?? 8;
            $columns = $settings['columns'] ?? 4;
            $categoryId = $settings['category_id'] ?? '';
            $title = $settings['title'] ?? 'Our Products';
            $showTitle = $settings['show_title'] ?? true;
            $showViewAll = $settings['show_view_all'] ?? false;
            $viewAllText = $settings['view_all_text'] ?? 'View All Products';
            $viewAllLink = $settings['view_all_link'] ?? '/shop';
            $titleColor = $settings['title_color'] ?? '#1f2937';
            $titleFontSize = $settings['title_font_size'] ?? 'text-2xl';
            $spacingStyle = $getSpacingStyle($settings);
            
            $html = '<div class="product-preview-container" data-type="product-grid" data-limit="'.$limit.'" data-columns="'.$columns.'" data-category="'.$categoryId.'" data-show-title="'.($showTitle ? '1' : '0').'" data-title="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" data-title-color="'.$titleColor.'" data-title-font-size="'.$titleFontSize.'" data-show-view-all="'.($showViewAll ? '1' : '0').'" data-view-all-text="'.htmlspecialchars($viewAllText, ENT_QUOTES, 'UTF-8').'" data-view-all-link="'.$viewAllLink.'" style="'.$spacingStyle.'">';
            
            if ($showTitle || $showViewAll) {
                $html .= '<div class="flex items-center justify-between mb-6">';
                if ($showTitle) {
                    $html .= '<h2 class="'.$titleFontSize.' font-bold" style="color: '.$titleColor.'">'.e($title).'</h2>';
                } else {
                    $html .= '<div></div>';
                }
                if ($showViewAll) {
                    $html .= '<a href="'.$viewAllLink.'" class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition view-all-btn">'.e($viewAllText).'<i class="fas fa-arrow-right text-xs"></i></a>';
                }
                $html .= '</div>';
            }
            
            $html .= '<div class="product-grid-loading flex justify-center items-center py-8 text-gray-400">
                    <svg class="animate-spin h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading products...</span>
                </div>';
            
            $html .= '</div>';
            break;
            
        case 'product-carousel':
            $limit = $settings['limit'] ?? 10;
            $columns = $settings['columns'] ?? 4;
            $categoryId = $settings['category_id'] ?? '';
            $autoplay = $settings['autoplay'] ?? true;
            $arrows = $settings['arrows'] ?? true;
            $dots = $settings['dots'] ?? true;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="product-preview-container" data-type="product-carousel" data-limit="'.$limit.'" data-columns="'.$columns.'" data-category="'.$categoryId.'" data-autoplay="'.($autoplay ? 'true' : 'false').'" data-arrows="'.($arrows ? 'true' : 'false').'" data-dots="'.($dots ? 'true' : 'false').'" style="'.$spacingStyle.'">
                <div class="product-grid-loading flex justify-center items-center py-8 text-gray-400">
                    <svg class="animate-spin h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading carousel...</span>
                </div>
            </div>';
            break;
            
        case 'category-grid':
            $limit = $settings['limit'] ?? 6;
            $columns = $settings['columns'] ?? 3;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="category-preview-container" data-limit="'.$limit.'" data-columns="'.$columns.'" style="'.$spacingStyle.'">
                <div class="category-grid-loading flex justify-center items-center py-8 text-gray-400">
                    <svg class="animate-spin h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading categories...</span>
                </div>
            </div>';
            break;
            
        case 'featured-products':
            $limit = $settings['limit'] ?? 8;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="product-preview-container" data-type="featured-products" data-limit="'.$limit.'" data-columns="4" style="'.$spacingStyle.'">
                <div class="product-grid-loading flex justify-center items-center py-8 text-gray-400">
                    <svg class="animate-spin h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading products...</span>
                </div>
            </div>';
            break;
            
        case 'new-arrivals':
            $limit = $settings['limit'] ?? 8;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="product-preview-container" data-type="new-arrivals" data-limit="'.$limit.'" data-columns="4" style="'.$spacingStyle.'">
                <div class="product-grid-loading flex justify-center items-center py-8 text-gray-400">
                    <svg class="animate-spin h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading products...</span>
                </div>
            </div>';
            break;
            
        case 'newsletter':
            $title = $settings['title'] ?? 'Subscribe to Newsletter';
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="text-center bg-gray-50" style="'.$spacingStyle.';"><h3 class="text-xl font-semibold mb-2">'.e($title).'</h3><p class="text-gray-500">[Email input field]</p></div>';
            break;
            
        case 'faq':
            $items = $settings['items'] ?? [
                ['question' => 'What is your return policy?', 'answer' => 'We offer a 30-day return policy for all unused items.'],
                ['question' => 'How long does shipping take?', 'answer' => 'Standard shipping takes 3-5 business days.'],
            ];
            $title = $settings['title'] ?? 'Frequently Asked Questions';
            $titleColor = $settings['title_color'] ?? '#1f2937';
            $questionColor = $settings['question_color'] ?? '#1f2937';
            $answerColor = $settings['answer_color'] ?? '#6b7280';
            $borderColor = $settings['border_color'] ?? '#e5e7eb';
            $spacingStyle = $getSpacingStyle($settings);
            
            $html = '<div style="'.$spacingStyle.'">';
            if (!empty($title)) {
                $html .= '<h2 class="text-2xl font-bold mb-6 text-center" style="color: '.$titleColor.';">'.e($title).'</h2>';
            }
            $html .= '<div class="space-y-3">';
            foreach ($items as $item) {
                $html .= '<div class="border rounded-lg overflow-hidden" style="border-color: '.$borderColor.';">';
                $html .= '<div class="flex items-center justify-between p-4 cursor-pointer bg-white hover:bg-gray-50" style="color: '.$questionColor.';">';
                $html .= '<span class="font-medium">'.e($item['question'] ?? '').'</span>';
                $html .= '<svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                $html .= '</div>';
                $html .= '<div class="px-4 pb-4 hidden" style="color: '.$answerColor.';">'.e($item['answer'] ?? '').'</div>';
                $html .= '</div>';
            }
            $html .= '</div></div>';
            break;
            
        case 'video':
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div style="'.$spacingStyle.'"><p class="text-gray-500">🎬 Video Widget</p></div>';
            break;
            
        case 'spacer':
            $height = $settings['height'] ?? 32;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div style="'.$spacingStyle.' height: '.$height.'px; display: flex; align-items: center; justify-content: center; background: #f9fafb;"><span class="text-gray-400 text-sm">Spacer: '.$height.'px</span></div>';
            break;
            
        case 'divider':
            $color = $settings['color'] ?? '#e5e7eb';
            $margin = $settings['margin'] ?? 16;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div style="'.$spacingStyle.'"><hr style="border-color: '.$color.'; margin: '.$margin.'px 0;"></hr></div>';
            break;
            
        case 'two-columns':
            $gap = $settings['gap'] ?? 4;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="grid grid-cols-2 gap-'.$gap.'" style="'.$spacingStyle.'"><div class="bg-gray-100 p-4 text-center">Column 1</div><div class="bg-gray-100 p-4 text-center">Column 2</div></div>';
            break;
            
        case 'three-columns':
            $gap = $settings['gap'] ?? 4;
            $spacingStyle = $getSpacingStyle($settings);
            $html = '<div class="grid grid-cols-3 gap-'.$gap.'" style="'.$spacingStyle.'"><div class="bg-gray-100 p-4 text-center">Column 1</div><div class="bg-gray-100 p-4 text-center">Column 2</div><div class="bg-gray-100 p-4 text-center">Column 3</div></div>';
            break;
            
        case 'trust-badges':
            $bg = $settings['background'] ?? '#f9fafb';
            $items = $settings['items'] ?? [['text' => '100% Authentic', 'description' => 'Genuine products guaranteed'], ['text' => 'Fast Delivery', 'description' => 'Delivery within 24-48 hours'], ['text' => 'Secure Payment', 'description' => '100% secure transactions'], ['text' => 'Easy Returns', 'description' => 'Hassle-free return policy']];
            $html = '<div class="grid grid-cols-4 gap-4" style="background: '.$bg.'; padding: 24px;">';
            foreach ($items as $item) {
                $html .= '<div class="text-center p-4"><div class="w-12 h-12 rounded-full bg-rose-50 mx-auto mb-2 flex items-center justify-center"><svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div><div class="font-medium text-gray-800">'.($item['text'] ?? '').'</div><div class="text-xs text-gray-500 mt-1">'.($item['description'] ?? '').'</div></div>';
            }
            $html .= '</div>';
            break;
    }
    return $html;
}
@endphp

@push('scripts')
<script>
// Global variables
window.draggedWidget = null;
let widgets = {!! json_encode($page->widgets ?? []) !!};
let selectedWidgetIndex = null;
const categories = {!! \App\Models\Category::select('id', 'name')->get()->toJson() !!};

// Get CSRF token for API requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Default fetch options with credentials and CSRF
const fetchOptions = {
    credentials: 'include',
    headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
};

if (!Array.isArray(widgets)) {
    widgets = [];
}

console.log('Initial widgets:', widgets);

// Loader reference
const canvasLoader = document.getElementById('canvas-loader');

document.addEventListener('DOMContentLoaded', function() {
    console.log('Page builder loaded');
    
    initDragAndDrop();
    initColorPickers();
    
    // Load previews for existing widgets
    loadWidgetPreviews();
    
    // Hide loader and select first widget (without re-rendering)
    setTimeout(() => {
        if (widgets && widgets.length > 0) {
            selectWidgetForEdit(0);
        }
        // Hide loader after content is ready
        if (canvasLoader) {
            canvasLoader.style.display = 'none';
        }
    }, 100);
});

function selectWidgetForEdit(index) {
    selectedWidgetIndex = index;
    const widget = widgets[index];
    const settingsPanel = document.getElementById('widget-settings');
    const widgetNames = {
        hero: 'Hero Section',
        banner: 'Banner',
        title: 'Title',
        text: 'Text Block',
        'product-grid': 'Product Grid',
        'product-carousel': 'Product Carousel',
        'category-grid': 'Category Grid',
        'featured-products': 'Featured Products',
        'new-arrivals': 'New Arrivals',
        newsletter: 'Newsletter',
        faq: 'FAQ',
        video: 'Video',
        spacer: 'Spacer',
        divider: 'Divider',
        'two-columns': '2 Columns',
        'three-columns': '3 Columns',
        'trust-badges': 'Trust Badges'
    };
    document.getElementById('selected-widget-name').textContent = widgetNames[widget.id] || 'Widget';
    
    // Highlight selected widget
    document.querySelectorAll('.canvas-widget').forEach((el, i) => {
        if (i === index) {
            el.classList.add('selected');
        } else {
            el.classList.remove('selected');
        }
    });
    
    // Tab structure
    const tabHtml = `
        <div class="flex border-b border-gray-200 mb-4">
            <button type="button" onclick="switchTab('content', '${widget.id}')" class="tab-btn px-4 py-2 text-sm font-medium text-rose-500 border-b-2 border-rose-500" data-tab="content">Content</button>
            <button type="button" onclick="switchTab('style', '${widget.id}')" class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="style">Style</button>
            <button type="button" onclick="switchTab('advanced', '${widget.id}')" class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="advanced">Advanced</button>
        </div>
        <div id="tab-content" class="space-y-4"></div>
    `;
    
    settingsPanel.innerHTML = tabHtml;
    
    // Load content tab by default
    setTimeout(() => loadWidgetTab(widget, 'content'), 0);
}

// Load product and category previews
function loadWidgetPreviews() {
    const API_URL = '{{ url("/api") }}';
    
    // Load product previews
    document.querySelectorAll('.product-preview-container, .preview-product-grid, .preview-product-carousel').forEach(container => {
        if (container.dataset.loaded === 'true') return;
        container.dataset.loaded = 'true';
        
        const type = container.dataset.type;
        const limit = parseInt(container.dataset.limit) || 8;
        const columns = parseInt(container.dataset.columns) || 4;
        const categoryId = container.dataset.category;
        let url = `${API_URL}/products?limit=${limit}&per_page=${limit}`;
        
        if (categoryId) {
            url += `&category_id=${categoryId}`;
        }
        
        if (type === 'featured-products') {
            url += '&featured=1';
        } else if (type === 'new-arrivals') {
            url += '&sort_by=created_at&sort_order=desc';
        }
        
        fetch(url, fetchOptions)
            .then(res => res.json())
            .then(data => {
                const products = data.data?.data || data.data || [];
                if (type === 'product-carousel') {
                    renderCarouselPreview(container, products, columns);
                } else {
                    renderProductPreview(container, products, columns);
                }
            })
            .catch(() => {
                container.innerHTML = '<div class="text-center py-4 text-gray-400">Failed to load products</div>';
            });
    });
    
    // Load category previews
    document.querySelectorAll('.category-preview-container, .preview-category-grid').forEach(container => {
        if (container.dataset.loaded === 'true') return;
        container.dataset.loaded = 'true';
        
        const limit = parseInt(container.dataset.limit) || 6;
        const columns = parseInt(container.dataset.columns) || 3;
        const url = `${API_URL}/categories?limit=${limit}&per_page=${limit}`;
        
        fetch(url, fetchOptions)
            .then(res => res.json())
            .then(data => {
                const categories = data.data?.data || data.data || [];
                renderCategoryPreview(container, categories, columns);
            })
            .catch(() => {
                container.innerHTML = '<div class="text-center py-4 text-gray-400">Failed to load categories</div>';
            });
    });
}

function getImageUrl(img) {
    if (!img) return null;
    if (img.startsWith('http')) return img;
    // Remove leading slash if present
    if (img.startsWith('/')) {
        return '{{ url("/") }}' + img;
    }
    return '{{ url("/storage/") }}' + img;
}

const placeholderSvg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="300" height="400" viewBox="0 0 300 400"><rect fill="%23e5e7eb" width="300" height="400"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%239ca3af" font-family="sans-serif" font-size="14">No Image</text></svg>';
const categoryPlaceholderSvg = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300"><rect fill="%23e5e7eb" width="300" height="300"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%239ca3af" font-family="sans-serif" font-size="14">No Image</text></svg>';

function renderProductPreview(container, products, columns) {
    if (!products || products.length === 0) {
        const loadingDiv = container.querySelector('.product-grid-loading');
        if (loadingDiv) {
            loadingDiv.innerHTML = '<div class="text-center py-4 text-gray-400">No products found</div>';
        }
        return;
    }
    
    const limit = parseInt(container.dataset.limit) || 8;
    
    const productsHtml = `
        <div class="grid gap-4" style="grid-template-columns: repeat(${columns}, minmax(0, 1fr));">
            ${products.slice(0, limit).map(product => {
                const price = product.sale_price || product.price;
                const originalPrice = product.sale_price ? product.price : null;
                const isOnSale = product.sale_price && product.sale_price < product.price;
                const discount = isOnSale && originalPrice ? Math.round(((originalPrice - price) / originalPrice) * 100) : 0;
                const imageUrl = getImageUrl(product.image || (product.images && product.images[0]));
                
                return `
                    <div class="group">
                        <div class="relative overflow-hidden bg-gray-100 rounded-lg aspect-[3/4]">
                            ${imageUrl ? `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'">` : ''}
                            ${!imageUrl ? `<div class="absolute inset-0 flex items-center justify-center text-gray-400"><svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>` : ''}
                            ${isOnSale ? `<span class="absolute top-2 left-2 bg-rose-500 text-white text-xs px-2 py-1 rounded">-${discount}%</span>` : ''}
                            ${product.is_featured ? `<span class="absolute top-2 right-2 bg-gray-900 text-white text-xs px-2 py-1 rounded">Featured</span>` : ''}
                        </div>
                        <div class="mt-2">
                            <h4 class="text-sm font-medium text-gray-800 truncate">${product.name}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-sm font-semibold ${isOnSale ? 'text-rose-500' : 'text-gray-900'}">৳${price}</span>
                                ${isOnSale ? `<span class="text-xs text-gray-400 line-through">৳${originalPrice}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
    
    const loadingDiv = container.querySelector('.product-grid-loading');
    if (loadingDiv) {
        loadingDiv.outerHTML = productsHtml;
    }
}

function renderCarouselPreview(container, products, visibleCount) {
    if (!products || products.length === 0) {
        container.innerHTML = '<div class="text-center py-4 text-gray-400">No products found</div>';
        return;
    }
    
    const limit = parseInt(container.dataset.limit) || 10;
    const showArrows = container.dataset.arrows !== 'false';
    const showDots = container.dataset.dots !== 'false';
    
    const html = `
        <div class="relative">
            <div class="flex gap-4 overflow-hidden">
                ${products.slice(0, limit).map(product => {
                    const price = product.sale_price || product.price;
                    const originalPrice = product.sale_price ? product.price : null;
                    const isOnSale = product.sale_price && product.sale_price < product.price;
                    const discount = isOnSale && originalPrice ? Math.round(((originalPrice - price) / originalPrice) * 100) : 0;
                    const image = getImageUrl(product.image || (product.images && product.images[0]));
                    
                    return `
                        <div class="flex-none w-48 group">
                            <div class="relative overflow-hidden bg-gray-100 rounded-lg aspect-[3/4]">
                                ${image ? `<img src="${image}" alt="${product.name}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'">` : ''}
                                ${isOnSale ? `<span class="absolute top-2 left-2 bg-rose-500 text-white text-xs px-2 py-1 rounded">-${discount}%</span>` : ''}
                            </div>
                            <div class="mt-2">
                                <h4 class="text-sm font-medium text-gray-800 truncate">${product.name}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-sm font-semibold ${isOnSale ? 'text-rose-500' : 'text-gray-900'}">৳${price}</span>
                                    ${isOnSale ? `<span class="text-xs text-gray-400 line-through">৳${originalPrice}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
            ${showArrows ? `
                <button class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-2 w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-gray-600 hover:text-gray-900">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-2 w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-gray-600 hover:text-gray-900">
                    <i class="fas fa-chevron-right"></i>
                </button>
            ` : ''}
            ${showDots ? `
                <div class="flex justify-center gap-2 mt-4">
                    ${Array.from({length: Math.ceil(products.length / visibleCount)}).map((_, i) => `
                        <span class="w-2 h-2 rounded-full ${i === 0 ? 'bg-rose-500' : 'bg-gray-300'}"></span>
                    `).join('')}
                </div>
            ` : ''}
        </div>
    `;
    
    container.innerHTML = html;
}

function renderCategoryPreview(container, categories, columns) {
    const limit = parseInt(container.dataset.limit) || 6;
    const html = `
        <div class="grid gap-4" style="grid-template-columns: repeat(${columns}, minmax(0, 1fr));">
            ${categories && categories.length > 0 ? categories.slice(0, limit).map(category => {
                const imageUrl = getImageUrl(category.image);
                
                return `
                    <div class="group relative overflow-hidden rounded-lg aspect-square">
                        ${imageUrl ? `<img src="${imageUrl}" alt="${category.name}" class="w-full h-full object-cover" loading="lazy" onerror="this.style.display='none'">` : ''}
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                            <span class="text-white font-medium">${category.name}</span>
                        </div>
                    </div>
                `;
            }).join('') : '<div class="text-center py-4 text-gray-400">No categories found</div>'}
        </div>
    `;
    
    const loadingDiv = container.querySelector('.category-grid-loading');
    if (loadingDiv) {
        loadingDiv.outerHTML = html;
    } else {
        container.innerHTML = html;
    }
}

// Store for Pickr instances
const colorPickers = {};

function initColorPickers() {
    document.querySelectorAll('input[type="color"].pickr-enabled').forEach(input => {
        if (input.dataset.pickrInit) return;
        input.dataset.pickrInit = 'true';
        
        const wrapper = document.createElement('div');
        wrapper.className = 'color-picker-wrapper relative';
        wrapper.style.cssText = 'display: inline-block; width: 100%;';
        
        input.style.cssText = 'cursor: pointer; padding: 0; border: none; width: 100%; height: 40px; border-radius: 8px;';
        
        const parent = input.parentElement;
        parent.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        
        const picker = Pickr.create({
            el: input,
            theme: 'classic',
            default: input.value || '#000000',
            components: {
                preview: true,
                opacity: true,
                hue: true,
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: true,
                    input: true,
                    clear: true,
                    save: true
                }
            },
            swatches: [
                '#f43f5e', '#ec4899', '#d946ef', '#a855f7', '#8b5cf6',
                '#6366f1', '#3b82f6', '#0ea5e9', '#06b6d4', '#14b8a6',
                '#10b981', '#22c55e', '#84cc16', '#eab308', '#f59e0b',
                '#f97316', '#ef4444', '#1f2937', '#374151', '#6b7280',
                '#9ca3af', '#d1d5db', '#e5e7eb', '#f3f4f6', '#ffffff'
            ]
        });
        
        picker.on('change', (color, instance) => {
            const hex = color.toHEXA().join('');
            input.value = '#' + hex;
            const onchange = input.getAttribute('onchange');
            if (onchange) {
                // Extract and call the function
                const match = onchange.match(/updateWidgetSetting\(['"]([^'"]+)['"]/);
                if (match) {
                    updateWidgetSetting(match[1], input.value);
                }
            }
        });
        
        picker.on('save', (color, instance) => {
            const hex = color.toHEXA().join('');
            input.value = '#' + hex;
            const onchange = input.getAttribute('onchange');
            if (onchange) {
                const match = onchange.match(/updateWidgetSetting\(['"]([^'"]+)['"]/);
                if (match) {
                    updateWidgetSetting(match[1], input.value);
                }
            }
            picker.hide();
        });
        
        colorPickers[input.id || input.name] = picker;
    });
}

function initDragAndDrop() {
    const widgetList = document.getElementById('widget-list');
    const canvasArea = document.getElementById('canvas');
    
    // Set up drag events on widget items
    widgetList.querySelectorAll('.widget-drag-item').forEach(function(item) {
        item.addEventListener('dragstart', function(e) {
            const widgetType = this.getAttribute('data-widget-type');
            console.log('Drag started:', widgetType);
            
            // Set data for drag
            e.dataTransfer.setData('text/plain', widgetType);
            e.dataTransfer.effectAllowed = 'copy';
            
            // Store in window variable
            window.draggedWidget = widgetType;
            
            // Add visual feedback
            this.style.opacity = '0.5';
        });
        
        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
            window.draggedWidget = null;
        });
    });
    
    // Drop zone events on canvas area
    canvasArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        this.classList.add('drag-over');
    });
    
    canvasArea.addEventListener('dragleave', function(e) {
        if (!this.contains(e.relatedTarget)) {
            this.classList.remove('drag-over');
        }
    });
    
    canvasArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('drag-over');
        
        const widgetType = e.dataTransfer.getData('text/plain') || window.draggedWidget;
        console.log('Dropped widget:', widgetType);
        
        if (widgetType) {
            addWidget(widgetType);
            window.draggedWidget = null;
        }
    });
}

function addWidget(type) {
    console.log('Adding widget:', type);
    const widgetDefaults = getWidgetDefaults(type);
    widgets.push({
        id: type,
        settings: {...widgetDefaults}
    });
    console.log('Current widgets:', widgets);
    renderWidgets();
    editWidget(widgets.length - 1);
}

function getWidgetDefaults(type) {
    const defaults = {
        hero: { title: 'Hero Title', subtitle: 'Your subtitle here', button_text: 'Shop Now', button_url: '/shop', background: '#f3f4f6', text_color: '#1f2937', subtitle_color: '#6b7280', bg_type: 'color', bg_image: '', bg_position: 'center center', bg_size: 'cover', bg_repeat: 'no-repeat', text_align: 'center', overlay_opacity: 0, gradient: 'linear-gradient(135deg, #667eea, #764ba2)', gradient_type: 'linear', gradient_color1: '#667eea', gradient_color2: '#764ba2', gradient_position: 'top', gradient_angle: 135, title_font_size: '4xl', subtitle_font_size: 'lg', button_bg: '#f43f5e', button_bg_hover: '#e11d48', button_color: '#ffffff', button_color_hover: '#ffffff', padding_top: 80, padding_bottom: 80, margin_top: 0, margin_bottom: 0 },
        banner: { text: 'Banner Text Here', link: '', background: '#fce7f3', text_color: '#831843', padding_top: 48, padding_bottom: 48, margin_top: 0, margin_bottom: 0 },
        title: { text: 'Your Title Here', color: '#1f2937', font_size: '4xl', font_weight: 'bold', align: 'center', margin_bottom: '16px', padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        text: { content: 'Enter your text here...', text_color: '#374151', font_size: 'base', text_align: 'left', padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        'product-grid': { title: 'Our Products', show_title: true, category_id: '', limit: 8, columns: 4, show_view_all: false, view_all_text: 'View All Products', view_all_link: '/shop', title_color: '#1f2937', title_font_size: 'text-2xl', padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        'product-carousel': { title: 'Featured Products', category_id: '', limit: 10, columns: 4, autoplay: true, arrows: true, dots: true, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        'category-grid': { title: 'Categories', limit: 6, columns: 3, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        'featured-products': { title: 'Featured Products', limit: 8, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        'new-arrivals': { title: 'New Arrivals', limit: 8, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        newsletter: { title: 'Subscribe to Our Newsletter', subtitle: 'Get updates about new products', button_text: 'Subscribe', padding_top: 48, padding_bottom: 48, margin_top: 0, margin_bottom: 0 },
        faq: { title: 'Frequently Asked Questions', items: [{question: 'What is your return policy?', answer: 'We offer a 30-day return policy for all unused items.'}, {question: 'How long does shipping take?', answer: 'Standard shipping takes 3-5 business days.'}], title_color: '#1f2937', question_color: '#1f2937', answer_color: '#6b7280', border_color: '#e5e7eb', padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        video: { url: '', embed_id: '', padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        spacer: { height: 32, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        divider: { style: 'solid', color: '#e5e7eb', margin: 16, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        'two-columns': { gap: 4, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 },
        'three-columns': { gap: 4, padding_top: 20, padding_bottom: 20, margin_top: 0, margin_bottom: 0 }
    };
    return defaults[type] || {};
}

function renderWidgets() {
    const canvas = document.getElementById('canvas');
    const loaderHtml = `
        <div id="canvas-loader-inline" class="absolute inset-0 bg-white/90 z-20 flex items-center justify-center rounded-lg" style="display: flex;">
            <div class="text-center">
                <svg class="animate-spin h-8 w-8 text-rose-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-500 text-sm">Updating...</p>
            </div>
        </div>`;
    
    // Get the original loader element
    const originalLoader = document.getElementById('canvas-loader');
    
    if (widgets.length === 0) {
        canvas.innerHTML = `
            <div class="text-center py-20 text-gray-400" id="empty-canvas">
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <p class="text-lg">Drag widgets here or click + Add button</p>
            </div>`;
        if (originalLoader) originalLoader.style.display = 'none';
} else {
        // Wrap canvas content with loader
        let html = loaderHtml;
        widgets.forEach(function(widget, index) {
            html += `
            <div class="canvas-widget relative group mb-4 border-2 border-transparent rounded-lg cursor-pointer ${selectedWidgetIndex === index ? 'selected' : ''}" 
                 data-widget-index="${index}"
                 data-widget-type="${widget.id}"
                 onclick="selectWidgetForEdit(${index})">
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 flex gap-1 z-10">
                    <button type="button" onclick="event.stopPropagation(); editWidget(${index})" class="p-1.5 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button type="button" onclick="event.stopPropagation(); moveWidget(${index}, -1)" class="p-1.5 bg-gray-500 text-white rounded hover:bg-gray-600 ${index === 0 ? 'opacity-50 cursor-not-allowed' : ''}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </button>
                    <button type="button" onclick="event.stopPropagation(); moveWidget(${index}, 1)" class="p-1.5 bg-gray-500 text-white rounded hover:bg-gray-600 ${index === widgets.length - 1 ? 'opacity-50 cursor-not-allowed' : ''}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <button type="button" onclick="event.stopPropagation(); deleteWidget(${index})" class="p-1.5 bg-red-500 text-white rounded hover:bg-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                ${renderWidgetPreviewHtml(widget.id, widget.settings)}
            </div>`;
            });
        canvas.innerHTML = html;
        
        // Hide loader and load previews
        setTimeout(() => {
            const inlineLoader = document.getElementById('canvas-loader-inline');
            if (inlineLoader) inlineLoader.style.display = 'none';
            if (originalLoader) originalLoader.style.display = 'none';
            
            // Load previews for product/category widgets
            loadWidgetPreviews();
        }, 100);
    }
}

function renderWidgetPreviewHtml(type, settings) {
    settings = settings || {};
    
    const getSpacingStyle = (s) => {
        const pt = s.padding_top !== undefined ? s.padding_top : 20;
        const pb = s.padding_bottom !== undefined ? s.padding_bottom : 20;
        const mt = s.margin_top !== undefined ? s.margin_top : 0;
        const mb = s.margin_bottom !== undefined ? s.margin_bottom : 0;
        return 'padding-top: ' + pt + 'px; padding-bottom: ' + pb + 'px; margin-top: ' + mt + 'px; margin-bottom: ' + mb + 'px;';
    };
    
    switch(type) {
        case 'hero': {
            let bgStyle = '';
            if (settings.bg_type === 'gradient' && settings.gradient) {
                bgStyle = 'background: ' + settings.gradient + ';';
            } else if (settings.bg_type === 'image' && settings.bg_image) {
                bgStyle = 'background-image: url(' + settings.bg_image + '); background-position: ' + (settings.bg_position || 'center center') + '; background-size: ' + (settings.bg_size || 'cover') + '; background-repeat: ' + (settings.bg_repeat || 'no-repeat') + ';';
            } else {
                bgStyle = 'background-color: ' + (settings.background || '#f3f4f6') + ';';
            }
            
            const overlayOpacity = settings.overlay_opacity || 0;
            const hasOverlay = overlayOpacity > 0 && (settings.bg_type === 'image' || settings.bg_type === 'gradient');
            
            const heroAlign = settings.text_align || 'center';
            const heroTitleSize = settings.title_font_size ? 'text-' + settings.title_font_size : 'text-4xl';
            const subtitleSize = settings.subtitle_font_size ? 'text-' + settings.subtitle_font_size : 'text-xl';
            const btnBg = settings.button_bg || '#f43f5e';
            const btnBgHover = settings.button_bg_hover || '#e11d48';
            const btnColor = settings.button_color || '#ffffff';
            const spacingStyle = getSpacingStyle(settings);
            let heroHtml = '<div class="relative" style="' + bgStyle + ' ' + spacingStyle + '">';
            if (hasOverlay) {
                heroHtml += '<div class="absolute inset-0 bg-black" style="opacity: ' + (overlayOpacity / 100) + ';"></div>';
            }
            heroHtml += '<div class="relative z-10 max-w-4xl mx-auto" style="text-align: ' + heroAlign + ';">' +
                '<h2 class="' + heroTitleSize + ' font-bold mb-4" style="color: ' + (settings.text_color || '#1f2937') + ';">' + (settings.title || 'Hero Title') + '</h2>' +
                '<p class="' + subtitleSize + ' mb-4" style="color: ' + (settings.subtitle_color || '#6b7280') + ';">' + (settings.subtitle || 'Subtitle goes here') + '</p>';
            if (settings.button_text) {
                heroHtml += '<button style="background-color: ' + btnBg + '; color: ' + btnColor + ';" class="px-6 py-2 rounded-lg transition-colors" onmouseover="this.style.backgroundColor=\'' + btnBgHover + '\'" onmouseout="this.style.backgroundColor=\'' + btnBg + '\'">' + settings.button_text + '</button>';
            }
            heroHtml += '</div></div>';
            return heroHtml;
        }
            
        case 'banner': {
            const bannerSpacing = getSpacingStyle(settings);
            return '<div class="text-center" style="background: ' + (settings.background || '#fce7f3') + '; ' + bannerSpacing + ';">' +
                '<p class="text-2xl font-semibold">' + (settings.text || 'Banner Text') + '</p></div>';
        }
            
        case 'title': {
            const titleSize = settings.font_size || '4xl';
            const titleColor = settings.color || '#1f2937';
            const titleAlign = settings.align || 'center';
            const titleWeight = settings.font_weight || 'bold';
            const titleSpacing = getSpacingStyle(settings);
            return '<div style="' + titleSpacing + ' text-align: ' + titleAlign + ';"><h2 class="text-' + titleSize + ' font-' + titleWeight + '" style="color: ' + titleColor + '; margin-bottom: ' + (settings.margin_bottom || '16px') + ';">' + (settings.text || 'Your Title Here') + '</h2></div>';
        }
            
        case 'text': {
            const textSize = settings.font_size ? 'text-' + settings.font_size : 'text-base';
            const textColor = settings.text_color || '#374151';
            const textAlign = settings.text_align || 'left';
            const content = settings.content || 'Text content goes here...';
            const textSpacing = getSpacingStyle(settings);
            return '<div style="' + textSpacing + '"><div class="ql-editor-content" style="color: ' + textColor + '; text-align: ' + textAlign + ';"><style>.ql-editor-content h1 { font-size: 2em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content h2 { font-size: 1.5em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content h3 { font-size: 1.25em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content h4 { font-size: 1em; font-weight: bold; margin-bottom: 0.5em; }.ql-editor-content p, .ql-editor-content ul, .ql-editor-content ol { font-size: ' + (settings.font_size === 'sm' ? '0.875rem' : settings.font_size === 'lg' ? '1.125rem' : settings.font_size === 'xl' ? '1.25rem' : '1rem') + '; line-height: 1.6; margin-bottom: 0.5em; }</style>' + content + '</div></div>';
        }
            
        case 'product-grid': {
            const pgLimit = settings.limit || 8;
            const pgColumns = settings.columns || 4;
            const pgCategory = settings.category_id || '';
            const pgShowTitle = settings.show_title !== false;
            const pgTitle = settings.title || 'Our Products';
            const pgTitleColor = settings.title_color || '#1f2937';
            const pgTitleFontSize = settings.title_font_size || 'text-2xl';
            const pgShowViewAll = settings.show_view_all || false;
            const pgViewAllText = settings.view_all_text || 'View All Products';
            const pgViewAllLink = settings.view_all_link || '/shop';
            const pgSpacing = getSpacingStyle(settings);
            
            let pgHtml = '<div class="product-preview-container" data-type="product-grid" data-limit="' + pgLimit + '" data-columns="' + pgColumns + '" data-category="' + pgCategory + '" data-show-title="' + (pgShowTitle ? '1' : '0') + '" data-title="' + pgTitle.replace(/"/g, '&quot;') + '" data-title-color="' + pgTitleColor + '" data-title-font-size="' + pgTitleFontSize + '" data-show-view-all="' + (pgShowViewAll ? '1' : '0') + '" data-view-all-text="' + pgViewAllText.replace(/"/g, '&quot;') + '" data-view-all-link="' + pgViewAllLink + '" style="' + pgSpacing + '">';
            
            if (pgShowTitle || pgShowViewAll) {
                pgHtml += '<div class="flex items-center justify-between mb-6">';
                if (pgShowTitle) {
                    pgHtml += '<h2 class="' + pgTitleFontSize + ' font-bold" style="color: ' + pgTitleColor + '">' + pgTitle + '</h2>';
                } else {
                    pgHtml += '<div></div>';
                }
                if (pgShowViewAll) {
                    pgHtml += '<a href="' + pgViewAllLink + '" class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition view-all-btn">' + pgViewAllText + '<i class="fas fa-arrow-right text-xs"></i></a>';
                }
                pgHtml += '</div>';
            }
            
            pgHtml += '<div class="product-grid-loading flex justify-center items-center py-8 text-gray-400"><svg class="animate-spin h-8 w-8 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Loading products...</span></div>';
            
            pgHtml += '</div>';
            return pgHtml;
        }
            
        case 'product-carousel': {
            const pcLimit = settings.limit || 10;
            const pcColumns = settings.columns || 4;
            const pcCategory = settings.category_id || '';
            const pcSpacing = getSpacingStyle(settings);
            const showArrows = settings.arrows !== false;
            const showDots = settings.dots !== false;
            return '<div class="product-preview-container" data-type="product-carousel" data-limit="' + pcLimit + '" data-columns="' + pcColumns + '" data-category="' + pcCategory + '" data-autoplay="' + (settings.autoplay ? 'true' : 'false') + '" data-arrows="' + (showArrows ? 'true' : 'false') + '" data-dots="' + (showDots ? 'true' : 'false') + '" style="' + pcSpacing + '"><div class="product-grid-loading flex justify-center py-4"><span class="text-gray-400">Loading carousel...</span></div></div>';
        }
            
        case 'category-grid': {
            const cgLimit = settings.limit || 6;
            const cgColumns = settings.columns || 3;
            const cgSpacing = getSpacingStyle(settings);
            return '<div class="category-preview-container" data-limit="' + cgLimit + '" data-columns="' + cgColumns + '" style="' + cgSpacing + '"><div class="category-grid-loading flex justify-center py-4"><span class="text-gray-400">Loading categories...</span></div></div>';
        }
            
        case 'featured-products': {
            const fpLimit = settings.limit || 8;
            const fpSpacing = getSpacingStyle(settings);
            return '<div class="product-preview-container" data-type="featured-products" data-limit="' + fpLimit + '" data-columns="4" style="' + fpSpacing + '"><div class="product-grid-loading flex justify-center py-4"><span class="text-gray-400">Loading products...</span></div></div>';
        }
            
        case 'new-arrivals': {
            const naLimit = settings.limit || 8;
            const naSpacing = getSpacingStyle(settings);
            return '<div class="product-preview-container" data-type="new-arrivals" data-limit="' + naLimit + '" data-columns="4" style="' + naSpacing + '"><div class="product-grid-loading flex justify-center py-4"><span class="text-gray-400">Loading products...</span></div></div>';
        }
            
        case 'newsletter': {
            const nlSpacing = getSpacingStyle(settings);
            return '<div class="text-center bg-gray-50" style="' + nlSpacing + '">' +
                '<h3 class="text-xl font-semibold mb-2">' + (settings.title || 'Subscribe to Newsletter') + '</h3>' +
                '<p class="text-gray-500">[Email input field]</p></div>';
        }
        
        case 'faq': {
            const faqSpacing = getSpacingStyle(settings);
            const faqTitle = settings.title || 'Frequently Asked Questions';
            const faqTitleColor = settings.title_color || '#1f2937';
            const faqQuestionColor = settings.question_color || '#1f2937';
            const faqAnswerColor = settings.answer_color || '#6b7280';
            const faqBorderColor = settings.border_color || '#e5e7eb';
            const faqItems = settings.items || [{question: 'What is your return policy?', answer: 'We offer a 30-day return policy for all unused items.'}, {question: 'How long does shipping take?', answer: 'Standard shipping takes 3-5 business days.'}];
            
            let faqHtml = '<div style="' + faqSpacing + '">';
            if (faqTitle) {
                faqHtml += '<h2 class="text-2xl font-bold mb-6 text-center" style="color: ' + faqTitleColor + ';">' + faqTitle + '</h2>';
            }
            faqHtml += '<div class="space-y-3">';
            faqItems.forEach((item, index) => {
                faqHtml += '<div class="border rounded-lg overflow-hidden" style="border-color: ' + faqBorderColor + ';">';
                faqHtml += '<div class="flex items-center justify-between p-4 cursor-pointer bg-white hover:bg-gray-50" style="color: ' + faqQuestionColor + ';" onclick="this.nextElementSibling.classList.toggle(\'hidden\'); this.querySelector(\'svg\').classList.toggle(\'rotate-180\');">';
                faqHtml += '<span class="font-medium">' + (item.question || '') + '</span>';
                faqHtml += '<svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
                faqHtml += '</div>';
                faqHtml += '<div class="px-4 pb-4 hidden" style="color: ' + faqAnswerColor + ';">' + (item.answer || '') + '</div>';
                faqHtml += '</div>';
            });
            faqHtml += '</div></div>';
            return faqHtml;
        }
             
        case 'video': {
            const videoSpacing = getSpacingStyle(settings);
            return '<div style="' + videoSpacing + '"><p class="text-gray-500">🎬 Video Widget</p></div>';
        }
             
        case 'spacer': {
            const spacerSpacing = getSpacingStyle(settings);
            return '<div style="' + spacerSpacing + ' height: ' + (settings.height || 32) + 'px; display: flex; align-items: center; justify-content: center; background: #f9fafb;"><span class="text-gray-400 text-sm">Spacer: ' + (settings.height || 32) + 'px</span></div>';
        }
            
        case 'divider': {
            const dividerSpacing = getSpacingStyle(settings);
            return '<div style="' + dividerSpacing + '"><hr style="border: none; border-top: ' + (settings.thickness || 1) + 'px ' + (settings.style || 'solid') + ' ' + (settings.color || '#e5e7eb') + '; margin: ' + (settings.margin || 16) + 'px 0;"></div>';
        }
            
        case 'two-columns': {
            const twoColSpacing = getSpacingStyle(settings);
            return '<div style="' + twoColSpacing + '"><div class="grid grid-cols-2 gap-' + (settings.gap || 4) + '"><div class="bg-gray-100 p-4 text-center">Column 1</div><div class="bg-gray-100 p-4 text-center">Column 2</div></div></div>';
        }
            
        case 'three-columns': {
            const threeColSpacing = getSpacingStyle(settings);
            return '<div style="' + threeColSpacing + '"><div class="grid grid-cols-3 gap-' + (settings.gap || 4) + '"><div class="bg-gray-100 p-4 text-center">Column 1</div><div class="bg-gray-100 p-4 text-center">Column 2</div><div class="bg-gray-100 p-4 text-center">Column 3</div></div></div>';
        }
            
        case 'trust-badges': {
            const badgesBg = settings.background || '#f9fafb';
            const badges = settings.items || [
                {text: '100% Authentic', description: 'Genuine products guaranteed'},
                {text: 'Fast Delivery', description: 'Delivery within 24-48 hours'},
                {text: 'Secure Payment', description: '100% secure transactions'},
                {text: 'Easy Returns', description: 'Hassle-free return policy'}
            ];
            let badgesHtml = '<div class="grid grid-cols-4 gap-4" style="background: ' + badgesBg + '; padding: 24px;">';
            badges.forEach(item => {
                badgesHtml += '<div class="text-center p-4"><div class="w-12 h-12 rounded-full bg-rose-50 mx-auto mb-2 flex items-center justify-center"><svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div><div class="font-medium text-gray-800">' + (item.text || '') + '</div><div class="text-xs text-gray-500 mt-1">' + (item.description || '') + '</div></div>';
            });
            badgesHtml += '</div>';
            return badgesHtml;
        }
            
        default:
            return '<div style="padding: 20px 0;" class="text-gray-500">Widget: ' + type + '</div>';
    }
}

function editWidget(index) {
    selectedWidgetIndex = index;
    const widget = widgets[index];
    const settingsPanel = document.getElementById('widget-settings');
    const widgetNames = {
        hero: 'Hero Section',
        banner: 'Banner',
        title: 'Title',
        text: 'Text Block',
        'product-grid': 'Product Grid',
        'product-carousel': 'Product Carousel',
        'category-grid': 'Category Grid',
        'featured-products': 'Featured Products',
        'new-arrivals': 'New Arrivals',
        newsletter: 'Newsletter',
        faq: 'FAQ',
        video: 'Video',
        spacer: 'Spacer',
        divider: 'Divider',
        'two-columns': '2 Columns',
        'three-columns': '3 Columns',
        'trust-badges': 'Trust Badges'
    };
    document.getElementById('selected-widget-name').textContent = widgetNames[widget.id] || 'Widget';
    
    // Tab structure
    const tabHtml = `
        <div class="flex border-b border-gray-200 mb-4">
            <button type="button" onclick="switchTab('content', '${widget.id}')" class="tab-btn px-4 py-2 text-sm font-medium text-rose-500 border-b-2 border-rose-500" data-tab="content">Content</button>
            <button type="button" onclick="switchTab('style', '${widget.id}')" class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="style">Style</button>
            <button type="button" onclick="switchTab('advanced', '${widget.id}')" class="tab-btn px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="advanced">Advanced</button>
        </div>
        <div id="tab-content" class="space-y-4"></div>
    `;
    
    settingsPanel.innerHTML = tabHtml;
    renderWidgets();
    
    // Load content tab by default
    setTimeout(() => loadWidgetTab(widget, 'content'), 0);
}

function getSpacingOptions(widget) {
    return `
        <div class="border-t pt-4 mt-4">
            <p class="text-sm font-medium text-gray-700 mb-3">Spacing</p>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Padding Top (px)</label>
                    <input type="number" class="w-full px-2 py-1 border rounded text-sm" value="${widget.settings.padding_top || 20}" min="0" max="200" onchange="updateWidgetSetting('padding_top', parseInt(this.value))">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Padding Bottom (px)</label>
                    <input type="number" class="w-full px-2 py-1 border rounded text-sm" value="${widget.settings.padding_bottom || 20}" min="0" max="200" onchange="updateWidgetSetting('padding_bottom', parseInt(this.value))">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Margin Top (px)</label>
                    <input type="number" class="w-full px-2 py-1 border rounded text-sm" value="${widget.settings.margin_top || 0}" min="0" max="200" onchange="updateWidgetSetting('margin_top', parseInt(this.value))">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Margin Bottom (px)</label>
                    <input type="number" class="w-full px-2 py-1 border rounded text-sm" value="${widget.settings.margin_bottom || 0}" min="0" max="200" onchange="updateWidgetSetting('margin_bottom', parseInt(this.value))">
                </div>
            </div>
        </div>
    `;
}

function switchTab(tab, widgetType) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        if (btn.dataset.tab === tab) {
            btn.classList.add('text-rose-500', 'border-b-2', 'border-rose-500');
            btn.classList.remove('text-gray-500');
        } else {
            btn.classList.remove('text-rose-500', 'border-b-2', 'border-rose-500');
            btn.classList.add('text-gray-500');
        }
    });
    
    loadWidgetTab(widgets[selectedWidgetIndex], tab);
}

function loadWidgetTab(widget, tab) {
    const tabContent = document.getElementById('tab-content');
    if (!tabContent) return;
    
    let html = '';
    
    switch(widget.id) {
        case 'hero':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.title || ''}" onchange="updateWidgetSetting('title', this.value)">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title Font Size</label>
                            <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('title_font_size', this.value)">
                                <option value="2xl" ${widget.settings.title_font_size === '2xl' ? 'selected' : ''}>2xl</option>
                                <option value="3xl" ${widget.settings.title_font_size === '3xl' ? 'selected' : ''}>3xl</option>
                                <option value="4xl" ${(widget.settings.title_font_size || '4xl') === '4xl' ? 'selected' : ''}>4xl</option>
                                <option value="5xl" ${widget.settings.title_font_size === '5xl' ? 'selected' : ''}>5xl</option>
                                <option value="6xl" ${widget.settings.title_font_size === '6xl' ? 'selected' : ''}>6xl</option>
                                <option value="7xl" ${widget.settings.title_font_size === '7xl' ? 'selected' : ''}>7xl</option>
                                <option value="8xl" ${widget.settings.title_font_size === '8xl' ? 'selected' : ''}>8xl</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle Font Size</label>
                            <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('subtitle_font_size', this.value)">
                                <option value="sm" ${widget.settings.subtitle_font_size === 'sm' ? 'selected' : ''}>sm</option>
                                <option value="base" ${widget.settings.subtitle_font_size === 'base' ? 'selected' : ''}>base</option>
                                <option value="lg" ${(widget.settings.subtitle_font_size || 'lg') === 'lg' ? 'selected' : ''}>lg</option>
                                <option value="xl" ${widget.settings.subtitle_font_size === 'xl' ? 'selected' : ''}>xl</option>
                                <option value="2xl" ${widget.settings.subtitle_font_size === '2xl' ? 'selected' : ''}>2xl</option>
                                <option value="3xl" ${widget.settings.subtitle_font_size === '3xl' ? 'selected' : ''}>3xl</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <textarea class="w-full px-3 py-2 border rounded-lg" rows="2" onchange="updateWidgetSetting('subtitle', this.value)">${widget.settings.subtitle || ''}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.button_text || ''}" onchange="updateWidgetSetting('button_text', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button URL</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.button_url || ''}" placeholder="/shop" onchange="updateWidgetSetting('button_url', this.value)">
                    </div>
                    <div class="border-t pt-4 mt-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">Button Style</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Background</label>
                                <input type="color" class="pickr-enabled w-full h-8 border rounded" value="${widget.settings.button_bg || '#f43f5e'}" onchange="updateWidgetSetting('button_bg', this.value)">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Background Hover</label>
                                <input type="color" class="pickr-enabled w-full h-8 border rounded" value="${widget.settings.button_bg_hover || '#e11d48'}" onchange="updateWidgetSetting('button_bg_hover', this.value)">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Text Color</label>
                                <input type="color" class="pickr-enabled w-full h-8 border rounded" value="${widget.settings.button_color || '#ffffff'}" onchange="updateWidgetSetting('button_color', this.value)">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Text Hover</label>
                                <input type="color" class="pickr-enabled w-full h-8 border rounded" value="${widget.settings.button_color_hover || '#ffffff'}" onchange="updateWidgetSetting('button_color_hover', this.value)">
                            </div>
                        </div>
                    </div>
                `;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.text_color || '#1f2937'}" onchange="updateWidgetSetting('text_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.subtitle_color || '#6b7280'}" onchange="updateWidgetSetting('subtitle_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Background Type</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('bg_type', this.value); toggleBgFields(this.value);">
                            <option value="color" ${(widget.settings.bg_type || 'color') === 'color' ? 'selected' : ''}>Solid Color</option>
                            <option value="gradient" ${widget.settings.bg_type === 'gradient' ? 'selected' : ''}>Gradient</option>
                            <option value="image" ${widget.settings.bg_type === 'image' ? 'selected' : ''}>Background Image</option>
                        </select>
                    </div>
                    <div id="hero-bg-color" class="${widget.settings.bg_type === 'image' || widget.settings.bg_type === 'gradient' ? 'hidden' : ''}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.background || '#f3f4f6'}" onchange="updateWidgetSetting('background', this.value)">
                    </div>
                    <div id="hero-gradient" class="${widget.settings.bg_type !== 'gradient' ? 'hidden' : ''}">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gradient Type</label>
                                <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('gradient_type', this.value); buildGradientCSS();">
                                    <option value="linear" ${widget.settings.gradient_type === 'linear' ? 'selected' : ''}>Linear</option>
                                    <option value="radial" ${widget.settings.gradient_type === 'radial' ? 'selected' : ''}>Radial</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Color 1</label>
                                    <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.gradient_color1 || '#667eea'}" onchange="updateWidgetSetting('gradient_color1', this.value); buildGradientCSS();">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Color 2</label>
                                    <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.gradient_color2 || '#764ba2'}" onchange="updateWidgetSetting('gradient_color2', this.value); buildGradientCSS();">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                    <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('gradient_position', this.value); buildGradientCSS();">
                                        <option value="top" ${(widget.settings.gradient_position || 'top') === 'top' ? 'selected' : ''}>Top</option>
                                        <option value="bottom" ${widget.settings.gradient_position === 'bottom' ? 'selected' : ''}>Bottom</option>
                                        <option value="left" ${widget.settings.gradient_position === 'left' ? 'selected' : ''}>Left</option>
                                        <option value="right" ${widget.settings.gradient_position === 'right' ? 'selected' : ''}>Right</option>
                                        <option value="top left" ${widget.settings.gradient_position === 'top left' ? 'selected' : ''}>Top Left</option>
                                        <option value="top right" ${widget.settings.gradient_position === 'top right' ? 'selected' : ''}>Top Right</option>
                                        <option value="bottom left" ${widget.settings.gradient_position === 'bottom left' ? 'selected' : ''}>Bottom Left</option>
                                        <option value="bottom right" ${widget.settings.gradient_position === 'bottom right' ? 'selected' : ''}>Bottom Right</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Angle</label>
                                    <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.gradient_angle || 135}" min="0" max="360" onchange="updateWidgetSetting('gradient_angle', parseInt(this.value)); buildGradientCSS();">
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Preview: <span id="gradient-preview" class="inline-block w-full h-6 rounded" style="background: ${widget.settings.gradient || 'linear-gradient(135deg, #667eea, #764ba2)'}"></span></p>
                            </div>
                        </div>
                    </div>
                    <div id="hero-bg-image-section" class="${widget.settings.bg_type !== 'image' ? 'hidden' : ''}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Background Image URL</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg text-xs mb-2" value="${widget.settings.bg_image || ''}" placeholder="https://..." onchange="updateWidgetSetting('bg_image', this.value)">
                        <div class="flex items-center gap-2">
                            <input type="file" accept="image/*" class="hidden" id="hero-bg-upload" onchange="uploadHeroBgImage(this)">
                            <label for="hero-bg-upload" class="px-3 py-1.5 bg-rose-500 text-white text-xs rounded cursor-pointer hover:bg-rose-600">
                                <i class="fas fa-upload mr-1"></i> Upload
                            </label>
                            ${widget.settings.bg_image ? `<button type="button" onclick="removeHeroBgImage()" class="px-3 py-1.5 bg-gray-500 text-white text-xs rounded hover:bg-gray-600"><i class="fas fa-times"></i></button>` : ''}
                        </div>
                        ${widget.settings.bg_image ? `<img src="${widget.settings.bg_image}" class="mt-2 w-full h-20 object-cover rounded border">` : ''}
                    </div>
                    <div id="hero-bg-position" class="${widget.settings.bg_type !== 'image' ? 'hidden' : ''}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image Position</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('bg_position', this.value)">
                            <option value="center center" ${(widget.settings.bg_position || 'center center') === 'center center' ? 'selected' : ''}>Center Center</option>
                            <option value="center top" ${widget.settings.bg_position === 'center top' ? 'selected' : ''}>Center Top</option>
                            <option value="left center" ${widget.settings.bg_position === 'left center' ? 'selected' : ''}>Left Center</option>
                            <option value="right center" ${widget.settings.bg_position === 'right center' ? 'selected' : ''}>Right Center</option>
                        </select>
                    </div>
                    <div id="hero-bg-size" class="${widget.settings.bg_type !== 'image' ? 'hidden' : ''}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image Size</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('bg_size', this.value)">
                            <option value="cover" ${(widget.settings.bg_size || 'cover') === 'cover' ? 'selected' : ''}>Cover</option>
                            <option value="contain" ${widget.settings.bg_size === 'contain' ? 'selected' : ''}>Contain</option>
                            <option value="100% 100%" ${widget.settings.bg_size === '100% 100%' ? 'selected' : ''}>Full Width</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.text_color || '#1f2937'}" onchange="updateWidgetSetting('text_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Text Align</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('text_align', this.value)">
                            <option value="center" ${(widget.settings.text_align || 'center') === 'center' ? 'selected' : ''}>Center</option>
                            <option value="left" ${widget.settings.text_align === 'left' ? 'selected' : ''}>Left</option>
                            <option value="right" ${widget.settings.text_align === 'right' ? 'selected' : ''}>Right</option>
                            <option value="justify" ${widget.settings.text_align === 'justify' ? 'selected' : ''}>Justify</option>
                        </select>
                    </div>
                `;
            } else if (tab === 'advanced') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Overlay Opacity: <span id="overlay-value">${widget.settings.overlay_opacity || 0}</span>%</label>
                        <input type="range" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" value="${widget.settings.overlay_opacity || 0}" min="0" max="100" onchange="updateWidgetSetting('overlay_opacity', parseInt(this.value)); document.getElementById('overlay-value').textContent = this.value;">
                        <p class="text-xs text-gray-500 mt-1">Works with Image and Gradient backgrounds</p>
                    </div>
                    ${getSpacingOptions(widget)}
                `;
            }
            break;
            
        case 'banner':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banner Text</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.text || ''}" onchange="updateWidgetSetting('text', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link URL</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.link || ''}" placeholder="/shop" onchange="updateWidgetSetting('link', this.value)">
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.background || '#fce7f3'}" onchange="updateWidgetSetting('background', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.text_color || '#831843'}" onchange="updateWidgetSetting('text_color', this.value)">
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'title':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title Text</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.text || ''}" onchange="updateWidgetSetting('text', this.value)">
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.color || '#1f2937'}" onchange="updateWidgetSetting('color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Font Size</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('font_size', this.value)">
                            <option value="2xl" ${widget.settings.font_size === '2xl' ? 'selected' : ''}>2xl</option>
                            <option value="3xl" ${widget.settings.font_size === '3xl' ? 'selected' : ''}>3xl</option>
                            <option value="4xl" ${(widget.settings.font_size || '4xl') === '4xl' ? 'selected' : ''}>4xl</option>
                            <option value="5xl" ${widget.settings.font_size === '5xl' ? 'selected' : ''}>5xl</option>
                            <option value="6xl" ${widget.settings.font_size === '6xl' ? 'selected' : ''}>6xl</option>
                            <option value="7xl" ${widget.settings.font_size === '7xl' ? 'selected' : ''}>7xl</option>
                            <option value="8xl" ${widget.settings.font_size === '8xl' ? 'selected' : ''}>8xl</option>
                            <option value="9xl" ${widget.settings.font_size === '9xl' ? 'selected' : ''}>9xl</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Font Weight</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('font_weight', this.value)">
                            <option value="normal" ${widget.settings.font_weight === 'normal' ? 'selected' : ''}>Normal</option>
                            <option value="medium" ${widget.settings.font_weight === 'medium' ? 'selected' : ''}>Medium</option>
                            <option value="semibold" ${widget.settings.font_weight === 'semibold' ? 'selected' : ''}>Semibold</option>
                            <option value="bold" ${(widget.settings.font_weight || 'bold') === 'bold' ? 'selected' : ''}>Bold</option>
                            <option value="extrabold" ${widget.settings.font_weight === 'extrabold' ? 'selected' : ''}>Extra Bold</option>
                        </select>
                    </div>`;
            } else {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Text Align</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('align', this.value)">
                            <option value="left" ${widget.settings.align === 'left' ? 'selected' : ''}>Left</option>
                            <option value="center" ${(widget.settings.align || 'center') === 'center' ? 'selected' : ''}>Center</option>
                            <option value="right" ${widget.settings.align === 'right' ? 'selected' : ''}>Right</option>
                            <option value="justify" ${widget.settings.align === 'justify' ? 'selected' : ''}>Justify</option>
                        </select>
                    </div>
                    ${getSpacingOptions(widget)}`;
            }
            break;
            
        case 'text':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <div id="text-editor-${selectedWidgetIndex}" class="bg-white border rounded-lg"></div>
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Text Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.text_color || '#374151'}" onchange="updateWidgetSetting('text_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Font Size</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('font_size', this.value)">
                            <option value="sm" ${widget.settings.font_size === 'sm' ? 'selected' : ''}>Small</option>
                            <option value="base" ${(widget.settings.font_size || 'base') === 'base' ? 'selected' : ''}>Normal</option>
                            <option value="lg" ${widget.settings.font_size === 'lg' ? 'selected' : ''}>Large</option>
                            <option value="xl" ${widget.settings.font_size === 'xl' ? 'selected' : ''}>Extra Large</option>
                        </select>
                    </div>`;
            } else {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Text Align</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('text_align', this.value)">
                            <option value="left" ${(widget.settings.text_align || 'left') === 'left' ? 'selected' : ''}>Left</option>
                            <option value="center" ${widget.settings.text_align === 'center' ? 'selected' : ''}>Center</option>
                            <option value="right" ${widget.settings.text_align === 'right' ? 'selected' : ''}>Right</option>
                            <option value="justify" ${widget.settings.text_align === 'justify' ? 'selected' : ''}>Justify</option>
                        </select>
                    </div>
                    ${getSpacingOptions(widget)}`;
            }
            break;
            
        case 'product-grid':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.title || ''}" onchange="updateWidgetSetting('title', this.value)" placeholder="e.g., Featured Products">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="show_title" ${widget.settings.show_title !== false ? 'checked' : ''} onchange="updateWidgetSetting('show_title', this.checked)">
                        <label for="show_title" class="text-sm text-gray-700">Show Title</label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('category_id', this.value)">
                            <option value="">All Categories</option>
                            ${categories.map(cat => `<option value="${cat.id}" ${widget.settings.category_id == cat.id ? 'selected' : ''}>${cat.name}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Products Limit</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.limit || 8}" min="1" max="50" onchange="updateWidgetSetting('limit', parseInt(this.value))">
                    </div>
                    <div class="border-t pt-4 mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">View All Button</label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="checkbox" id="show_view_all" ${widget.settings.show_view_all ? 'checked' : ''} onchange="updateWidgetSetting('show_view_all', this.checked)">
                            <label for="show_view_all" class="text-sm text-gray-700">Show "View All" Button</label>
                        </div>
                        <div class="mb-2">
                            <label class="block text-xs text-gray-500 mb-1">Button Text</label>
                            <input type="text" class="w-full px-3 py-2 border rounded-lg text-sm" value="${widget.settings.view_all_text || 'View All Products'}" onchange="updateWidgetSetting('view_all_text', this.value)" placeholder="View All Products">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Button Link</label>
                            <input type="text" class="w-full px-3 py-2 border rounded-lg text-sm" value="${widget.settings.view_all_link || '/shop'}" onchange="updateWidgetSetting('view_all_link', this.value)" placeholder="/shop">
                        </div>
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Columns</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('columns', parseInt(this.value))">
                            <option value="2" ${widget.settings.columns == 2 ? 'selected' : ''}>2 Columns</option>
                            <option value="3" ${widget.settings.columns == 3 ? 'selected' : ''}>3 Columns</option>
                            <option value="4" ${(widget.settings.columns || 4) == 4 ? 'selected' : ''}>4 Columns</option>
                            <option value="5" ${widget.settings.columns == 5 ? 'selected' : ''}>5 Columns</option>
                            <option value="6" ${widget.settings.columns == 6 ? 'selected' : ''}>6 Columns</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title Color</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 px-3 py-2 border rounded-lg" value="${widget.settings.title_color || '#1f2937'}" id="title-color-${selectedWidgetIndex}">
                            <button type="button" onclick="initColorPicker('title-color-${selectedWidgetIndex}', '${widget.settings.title_color || '#1f2937'}')" class="px-3 py-2 bg-gray-100 border rounded-lg hover:bg-gray-200">
                                <i class="fas fa-palette"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title Font Size</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('title_font_size', this.value)">
                            <option value="text-lg" ${widget.settings.title_font_size === 'text-lg' ? 'selected' : ''}>Large</option>
                            <option value="text-xl" ${(widget.settings.title_font_size || 'text-2xl') === 'text-xl' ? 'selected' : ''}>Extra Large</option>
                            <option value="text-2xl" ${widget.settings.title_font_size === 'text-2xl' ? 'selected' : ''}>2XL</option>
                            <option value="text-3xl" ${widget.settings.title_font_size === 'text-3xl' ? 'selected' : ''}>3XL</option>
                        </select>
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'product-carousel':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.title || ''}" onchange="updateWidgetSetting('title', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('category_id', this.value)">
                            <option value="">All Categories</option>
                            ${categories.map(cat => `<option value="${cat.id}" ${widget.settings.category_id == cat.id ? 'selected' : ''}>${cat.name}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Products Limit</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.limit || 10}" min="1" max="50" onchange="updateWidgetSetting('limit', parseInt(this.value))">
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Visible Products</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('columns', parseInt(this.value))">
                            <option value="2" ${widget.settings.columns == 2 ? 'selected' : ''}>2 Products</option>
                            <option value="3" ${widget.settings.columns == 3 ? 'selected' : ''}>3 Products</option>
                            <option value="4" ${(widget.settings.columns || 4) == 4 ? 'selected' : ''}>4 Products</option>
                            <option value="5" ${widget.settings.columns == 5 ? 'selected' : ''}>5 Products</option>
                            <option value="6" ${widget.settings.columns == 6 ? 'selected' : ''}>6 Products</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <input type="checkbox" id="autoplay" ${widget.settings.autoplay ? 'checked' : ''} onchange="updateWidgetSetting('autoplay', this.checked)">
                        <label for="autoplay" class="text-sm text-gray-700">Auto Play</label>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" id="arrows" ${widget.settings.arrows !== false ? 'checked' : ''} onchange="updateWidgetSetting('arrows', this.checked)">
                        <label for="arrows" class="text-sm text-gray-700">Show Arrows</label>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" id="dots" ${widget.settings.dots !== false ? 'checked' : ''} onchange="updateWidgetSetting('dots', this.checked)">
                        <label for="dots" class="text-sm text-gray-700">Show Dots</label>
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'category-grid':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.title || ''}" onchange="updateWidgetSetting('title', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categories Limit</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.limit || 6}" min="1" max="24" onchange="updateWidgetSetting('limit', parseInt(this.value))">
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Columns</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('columns', parseInt(this.value))">
                            <option value="2" ${widget.settings.columns == 2 ? 'selected' : ''}>2 Columns</option>
                            <option value="3" ${(widget.settings.columns || 3) == 3 ? 'selected' : ''}>3 Columns</option>
                            <option value="4" ${widget.settings.columns == 4 ? 'selected' : ''}>4 Columns</option>
                            <option value="6" ${widget.settings.columns == 6 ? 'selected' : ''}>6 Columns</option>
                        </select>
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'featured-products':
        case 'new-arrivals':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.title || ''}" onchange="updateWidgetSetting('title', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Products Limit</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.limit || 8}" min="1" max="20" onchange="updateWidgetSetting('limit', parseInt(this.value))">
                    </div>`;
            } else {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 mb-2">Columns</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('columns', parseInt(this.value))">
                            <option value="2" ${widget.settings.columns == 2 ? 'selected' : ''}>2 Columns</option>
                            <option value="3" ${widget.settings.columns == 3 ? 'selected' : ''}>3 Columns</option>
                            <option value="4" ${(widget.settings.columns || 4) == 4 ? 'selected' : ''}>4 Columns</option>
                        </select>
                    </div>
                    ${getSpacingOptions(widget)}`;
            }
            break;
            
        case 'newsletter':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.title || ''}" onchange="updateWidgetSetting('title', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <textarea class="w-full px-3 py-2 border rounded-lg" rows="2" onchange="updateWidgetSetting('subtitle', this.value)">${widget.settings.subtitle || ''}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.button_text || 'Subscribe'}" onchange="updateWidgetSetting('button_text', this.value)">
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.bg_color || '#f9fafb'}" onchange="updateWidgetSetting('bg_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.button_color || '#f43f5e'}" onchange="updateWidgetSetting('button_color', this.value)">
                    </div>`;
            } else {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Form Width</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('form_width', this.value)">
                            <option value="sm" ${widget.settings.form_width === 'sm' ? 'selected' : ''}>Small</option>
                            <option value="md" ${(widget.settings.form_width || 'md') === 'md' ? 'selected' : ''}>Medium</option>
                            <option value="lg" ${widget.settings.form_width === 'lg' ? 'selected' : ''}>Large</option>
                            <option value="full" ${widget.settings.form_width === 'full' ? 'selected' : ''}>Full Width</option>
                        </select>
                    </div>
                    ${getSpacingOptions(widget)}`;
            }
            break;
            
        case 'faq':
            if (tab === 'content') {
                const faqItems = widget.settings.items || [];
                let itemsHtml = '';
                faqItems.forEach((item, index) => {
                    itemsHtml += `
                        <div class="border rounded-lg p-3 mb-2 bg-gray-50">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-sm font-medium text-gray-700">Q${index + 1}</span>
                                <button type="button" onclick="removeFaqItem(${index})" class="text-red-500 hover:text-red-700 text-sm ${faqItems.length <= 1 ? 'hidden' : ''}">×</button>
                            </div>
                            <input type="text" class="w-full px-2 py-1 border rounded mb-2 text-sm" value="${item.question || ''}" placeholder="Question" onchange="updateFaqItem(${index}, 'question', this.value)">
                            <textarea class="w-full px-2 py-1 border rounded text-sm" rows="2" placeholder="Answer" onchange="updateFaqItem(${index}, 'answer', this.value)">${item.answer || ''}</textarea>
                        </div>`;
                });
                
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.title || ''}" placeholder="Frequently Asked Questions" onchange="updateWidgetSetting('title', this.value)">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">FAQ Items</label>
                        <div id="faq-items-container">${itemsHtml}</div>
                        <button type="button" onclick="addFaqItem()" class="w-full px-3 py-2 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-rose-500 hover:text-rose-500 transition">
                            + Add FAQ Item
                        </button>
                    </div>`;
            } else if (tab === 'style') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.title_color || '#1f2937'}" onchange="updateWidgetSetting('title_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.question_color || '#1f2937'}" onchange="updateWidgetSetting('question_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Answer Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.answer_color || '#6b7280'}" onchange="updateWidgetSetting('answer_color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Border Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.border_color || '#e5e7eb'}" onchange="updateWidgetSetting('border_color', this.value)">
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'video':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                        <input type="text" class="w-full px-3 py-2 border rounded-lg text-xs" value="${widget.settings.url || ''}" placeholder="https://youtube.com/watch?v=..." onchange="updateWidgetSetting('url', this.value)">
                        <p class="text-xs text-gray-500 mt-1">Supports YouTube and Vimeo</p>
                    </div>`;
            } else {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aspect Ratio</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('aspect_ratio', this.value)">
                            <option value="16:9" ${(widget.settings.aspect_ratio || '16:9') === '16:9' ? 'selected' : ''}>16:9 (Widescreen)</option>
                            <option value="4:3" ${widget.settings.aspect_ratio === '4:3' ? 'selected' : ''}>4:3</option>
                            <option value="1:1" ${widget.settings.aspect_ratio === '1:1' ? 'selected' : ''}>1:1 (Square)</option>
                        </select>
                    </div>
                    ${getSpacingOptions(widget)}`;
            }
            break;
            
        case 'spacer':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Height (px)</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.height || 32}" min="0" max="500" onchange="updateWidgetSetting('height', parseInt(this.value))">
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500">Common heights: 16px (small), 32px (normal), 64px (large), 128px (xlarge)</p>
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'divider':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Divider Style</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('style', this.value)">
                            <option value="solid" ${widget.settings.style === 'solid' ? 'selected' : ''}>Solid</option>
                            <option value="dashed" ${widget.settings.style === 'dashed' ? 'selected' : ''}>Dashed</option>
                            <option value="dotted" ${widget.settings.style === 'dotted' ? 'selected' : ''}>Dotted</option>
                            <option value="double" ${widget.settings.style === 'double' ? 'selected' : ''}>Double</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Width</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('width', this.value)">
                            <option value="100%" ${(widget.settings.width || '100%') === '100%' ? 'selected' : ''}>Full Width</option>
                            <option value="75%" ${widget.settings.width === '75%' ? 'selected' : ''}>75%</option>
                            <option value="50%" ${widget.settings.width === '50%' ? 'selected' : ''}>50%</option>
                            <option value="25%" ${widget.settings.width === '25%' ? 'selected' : ''}>25%</option>
                        </select>
                    </div>`;
            } else {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="color" class="pickr-enabled w-full h-10 border rounded-lg" value="${widget.settings.color || '#e5e7eb'}" onchange="updateWidgetSetting('color', this.value)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thickness (px)</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.thickness || 1}" min="1" max="10" onchange="updateWidgetSetting('thickness', parseInt(this.value))">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Margin (px)</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.margin || 16}" min="0" max="100" onchange="updateWidgetSetting('margin', parseInt(this.value))">
                    </div>
                    ${getSpacingOptions(widget)}`;
            }
            break;
            
        case 'two-columns':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Column Ratio</label>
                        <select class="w-full px-3 py-2 border rounded-lg" onchange="updateWidgetSetting('ratio', this.value)">
                            <option value="1:1" ${(widget.settings.ratio || '1:1') === '1:1' ? 'selected' : ''}>50% / 50%</option>
                            <option value="1:2" ${widget.settings.ratio === '1:2' ? 'selected' : ''}>33% / 67%</option>
                            <option value="2:1" ${widget.settings.ratio === '2:1' ? 'selected' : ''}>67% / 33%</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gap (px)</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.gap || 16}" min="0" max="64" onchange="updateWidgetSetting('gap', parseInt(this.value))">
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'three-columns':
            if (tab === 'content') {
                html = `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gap (px)</label>
                        <input type="number" class="w-full px-3 py-2 border rounded-lg" value="${widget.settings.gap || 16}" min="0" max="64" onchange="updateWidgetSetting('gap', parseInt(this.value))">
                    </div>`;
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        case 'trust-badges':
            if (tab === 'content') {
                const badgeItems = widget.settings.items || [
                    {text: '100% Authentic', description: 'Genuine products guaranteed', icon: 'shield-check'},
                    {text: 'Fast Delivery', description: 'Delivery within 24-48 hours', icon: 'truck-fast'},
                    {text: 'Secure Payment', description: '100% secure transactions', icon: 'shield-alt'},
                    {text: 'Easy Returns', description: 'Hassle-free return policy', icon: 'undo'}
                ];
                const iconOptions = [
                    {value: 'shield-check', label: 'Shield Check'},
                    {value: 'truck-fast', label: 'Fast Delivery'},
                    {value: 'shield-alt', label: 'Secure'},
                    {value: 'undo', label: 'Returns'},
                    {value: 'headset', label: 'Support'},
                    {value: 'badge-check', label: 'Verified'},
                    {value: 'support', label: 'Help'},
                    {value: 'credit-card', label: 'Payment'}
                ];
                let badgeItemsHtml = '';
                badgeItems.forEach((item, idx) => {
                    let selectOptions = '';
                    iconOptions.forEach(opt => {
                        const isSelected = (item.icon || 'shield-check') === opt.value ? ' selected' : '';
                        selectOptions += '<option value="' + opt.value + '"' + isSelected + '>' + opt.label + '</option>';
                    });
                    badgeItemsHtml += '<div class="flex gap-2 items-start p-2 border rounded-lg mb-2">' +
                        '<div class="flex-1 space-y-2">' +
                        '<select class="w-full px-3 py-2 border rounded-lg" data-idx="' + idx + '" data-field="icon" onchange="updateBadgeItem(this)">' +
                        selectOptions +
                        '</select>' +
                        '<input type="text" class="w-full px-3 py-2 border rounded-lg" value="' + (item.text || '') + '" placeholder="Badge title" data-idx="' + idx + '" data-field="text" onchange="updateBadgeItem(this)">' +
                        '<input type="text" class="w-full px-3 py-2 border rounded-lg text-sm" value="' + (item.description || '') + '" placeholder="Short description" data-idx="' + idx + '" data-field="description" onchange="updateBadgeItem(this)">' +
                        '</div>' +
                        '<button type="button" onclick="removeBadgeItem(' + idx + ')" class="text-red-500 hover:text-red-700 p-1 mt-1">' +
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>' +
                        '</button>' +
                        '</div>';
                });
                html = '<div class="space-y-4">' +
                    '<div><label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>' +
                    '<input type="color" class="w-full h-10 rounded-lg border cursor-pointer" value="' + (widget.settings.background || '#f9fafb') + '" onchange="updateWidgetSetting(\'background\', this.value)"></div>' +
                    '<div><label class="block text-sm font-medium text-gray-700 mb-2">Trust Badge Items</label>' +
                    '<div id="badge-items-editor">' + badgeItemsHtml + '</div>' +
                    '<button type="button" onclick="addBadgeItem()" class="mt-2 text-sm text-rose-500 hover:text-rose-600">+ Add Badge</button></div>' +
                    '</div>';
            } else {
                html = getSpacingOptions(widget);
            }
            break;
            
        default:
            html = '<p class="text-gray-500 text-center py-4">No settings available for this widget</p>';
    }
    
    tabContent.innerHTML = html;
    
    // Initialize color pickers for new content
    setTimeout(() => initColorPickers(), 100);
    
    // Initialize Quill editor for text widget
    if (widget.id === 'text' && tab === 'content') {
        setTimeout(function() {
            const editorElement = document.getElementById('text-editor-' + selectedWidgetIndex);
            if (editorElement && window.Quill) {
                // Destroy existing Quill instance if any
                if (editorElement.__quill) {
                    editorElement.__quill = null;
                    editorElement.innerHTML = '';
                }
                
                const quill = new Quill('#text-editor-' + selectedWidgetIndex, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, 4, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['link'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Enter your text content...'
                });
                
                // Set existing content
                const existingContent = widget.settings.content || '';
                if (existingContent) {
                    quill.root.innerHTML = existingContent;
                }
                
                // Store reference
                editorElement.__quill = quill;
                
                // Update on change
                quill.on('text-change', function() {
                    const html = quill.root.innerHTML;
                    updateWidgetSetting('content', html);
                });
            }
        }, 200);
    }
}

function updateWidgetSetting(key, value) {
    if (selectedWidgetIndex !== null && widgets[selectedWidgetIndex]) {
        widgets[selectedWidgetIndex].settings[key] = value;
        
        // Reset preview loading for product/category widgets to reload with new settings
        if (['category_id', 'limit', 'columns'].includes(key)) {
            previewsLoaded = false;
        }
        
        renderWidgets();
    }
}

function addFaqItem() {
    if (selectedWidgetIndex !== null && widgets[selectedWidgetIndex]) {
        if (!widgets[selectedWidgetIndex].settings.items) {
            widgets[selectedWidgetIndex].settings.items = [];
        }
        widgets[selectedWidgetIndex].settings.items.push({
            question: '',
            answer: ''
        });
        loadWidgetTab(widgets[selectedWidgetIndex], 'content');
    }
}

function removeFaqItem(index) {
    if (selectedWidgetIndex !== null && widgets[selectedWidgetIndex]) {
        widgets[selectedWidgetIndex].settings.items.splice(index, 1);
        loadWidgetTab(widgets[selectedWidgetIndex], 'content');
    }
}

function updateBadgeItem(input) {
    if (selectedWidgetIndex !== null && widgets[selectedWidgetIndex]) {
        const idx = parseInt(input.getAttribute('data-idx'));
        const field = input.getAttribute('data-field') || 'text';
        const items = widgets[selectedWidgetIndex].settings.items || [];
        if (!items[idx]) items[idx] = {};
        items[idx][field] = input.value;
        widgets[selectedWidgetIndex].settings.items = items;
        renderWidgets();
    }
}

function addBadgeItem() {
    if (selectedWidgetIndex !== null && widgets[selectedWidgetIndex]) {
        const items = widgets[selectedWidgetIndex].settings.items || [];
        items.push({text: ''});
        widgets[selectedWidgetIndex].settings.items = items;
        loadWidgetTab(widgets[selectedWidgetIndex], 'content');
    }
}

function removeBadgeItem(index) {
    if (selectedWidgetIndex !== null && widgets[selectedWidgetIndex]) {
        const items = widgets[selectedWidgetIndex].settings.items || [];
        items.splice(index, 1);
        widgets[selectedWidgetIndex].settings.items = items;
        loadWidgetTab(widgets[selectedWidgetIndex], 'content');
    }
}

function updateFaqItem(index, field, value) {
    if (selectedWidgetIndex !== null && widgets[selectedWidgetIndex]) {
        if (!widgets[selectedWidgetIndex].settings.items) {
            widgets[selectedWidgetIndex].settings.items = [];
        }
        if (!widgets[selectedWidgetIndex].settings.items[index]) {
            widgets[selectedWidgetIndex].settings.items[index] = { question: '', answer: '' };
        }
        widgets[selectedWidgetIndex].settings.items[index][field] = value;
        renderWidgets();
    }
}

function buildGradientCSS() {
    if (selectedWidgetIndex === null) return;
    const settings = widgets[selectedWidgetIndex].settings;
    const type = settings.gradient_type || 'linear';
    const color1 = settings.gradient_color1 || '#667eea';
    const color2 = settings.gradient_color2 || '#764ba2';
    const position = settings.gradient_position || 'top';
    const angle = settings.gradient_angle || 135;
    
    let gradient;
    if (type === 'linear') {
        // Use position if it's a simple direction, otherwise use angle
        if (['top', 'bottom', 'left', 'right'].includes(position)) {
            gradient = 'linear-gradient(to ' + position + ', ' + color1 + ', ' + color2 + ')';
        } else {
            gradient = 'linear-gradient(' + position + ', ' + color1 + ', ' + color2 + ')';
        }
    } else {
        gradient = 'radial-gradient(circle, ' + color1 + ', ' + color2 + ')';
    }
    
    widgets[selectedWidgetIndex].settings.gradient = gradient;
    renderWidgets();
    
    // Update preview
    setTimeout(() => {
        const preview = document.getElementById('gradient-preview');
        if (preview) preview.style.background = gradient;
    }, 0);
}

function uploadHeroBgImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const formData = new FormData();
        formData.append('image', file);
        
        console.log('Uploading file:', file.name);
        
        fetch('/admin/upload/image', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.log('Error response:', text);
                    throw new Error('HTTP ' + response.status + ': ' + text);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Upload response:', data);
            if (data.url) {
                updateWidgetSetting('bg_image', data.url);
            } else if (data.error) {
                alert('Upload failed: ' + data.error);
            } else {
                alert('Upload failed: Unknown error');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            alert('Upload failed: ' + error.message);
        });
    }
}

function removeHeroBgImage() {
    updateWidgetSetting('bg_image', '');
}

function toggleBgFields(bgType) {
    const bgColor = document.getElementById('hero-bg-color');
    const gradient = document.getElementById('hero-gradient');
    const bgImage = document.getElementById('hero-bg-image-section');
    const bgPosition = document.getElementById('hero-bg-position');
    const bgSize = document.getElementById('hero-bg-size');
    const bgRepeat = document.getElementById('hero-bg-repeat');
    const overlay = document.getElementById('hero-overlay');
    
    if (bgColor) bgColor.classList.toggle('hidden', bgType !== 'color');
    if (gradient) gradient.classList.toggle('hidden', bgType !== 'gradient');
    if (bgImage) bgImage.classList.toggle('hidden', bgType !== 'image');
    if (bgPosition) bgPosition.classList.toggle('hidden', bgType !== 'image');
    if (bgSize) bgSize.classList.toggle('hidden', bgType !== 'image');
    if (bgRepeat) bgRepeat.classList.toggle('hidden', bgType !== 'image');
    if (overlay) overlay.classList.toggle('hidden', bgType !== 'image');
}

function moveWidget(index, direction) {
    if (direction === -1 && index === 0) return;
    if (direction === 1 && index === widgets.length - 1) return;
    
    const newIndex = index + direction;
    const temp = widgets[index];
    widgets[index] = widgets[newIndex];
    widgets[newIndex] = temp;
    
    selectedWidgetIndex = newIndex;
    renderWidgets();
    editWidget(newIndex);
}

function deleteWidget(index) {
    if (confirm('Are you sure you want to delete this widget?')) {
        widgets.splice(index, 1);
        selectedWidgetIndex = null;
        document.getElementById('widget-settings').innerHTML = '<p class="text-gray-400 text-center py-8">Click the edit button on a widget to change its settings</p>';
        document.getElementById('selected-widget-name').textContent = 'Select a widget to edit';
        renderWidgets();
    }
}

function previewPage() {
    const modal = document.getElementById('preview-modal');
    const content = document.getElementById('preview-content');
    
    let html = '';
    widgets.forEach(widget => {
        html += renderWidgetPreviewHtml(widget.id, widget.settings);
    });
    
    content.innerHTML = html || '<p class="text-gray-400 text-center py-8">No widgets to preview</p>';
    modal.classList.remove('hidden');
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
}
</script>
@endpush
