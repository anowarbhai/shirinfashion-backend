@extends('admin.layouts.master')

@section('title', 'SEO Settings')

@section('header', 'SEO Settings')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-search text-green-600 mr-2"></i>
                Search Engine Optimization (SEO)
            </h2>
            <p class="text-gray-600 text-sm mt-1">
                Optimize your website for search engines to improve visibility and rankings.
            </p>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.marketing.seo.update') }}" class="p-6" id="seoForm">
            @csrf
            
            <!-- Google Search Preview -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fab fa-google text-blue-500 mr-2"></i>
                    Google Search Preview
                </h3>
                
                <div class="bg-white border border-gray-200 rounded-lg p-4 max-w-2xl">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 text-xs">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="text-sm">
                            <div class="text-gray-800 font-medium">shirinfashion.com</div>
                            <div class="text-green-700 text-xs">https://shirinfashion.com › ...</div>
                        </div>
                    </div>
                    <div class="text-blue-800 text-xl hover:underline cursor-pointer mb-1" id="previewTitle" style="line-height: 1.3;">
                        {{ $settings['seo_home_title'] }}
                    </div>
                    <div class="text-gray-600 text-sm leading-relaxed" id="previewDescription">
                        {{ Str::limit($settings['seo_home_description'], 160) }}
                    </div>
                </div>
                
                <div class="mt-3 flex items-center gap-4 text-xs text-gray-500">
                    <span id="titleLength" class="{{ strlen($settings['seo_home_title']) > 60 ? 'text-red-500' : 'text-green-600' }}">
                        <i class="fas fa-ruler-horizontal mr-1"></i>
                        Title: {{ strlen($settings['seo_home_title']) }}/60 characters
                    </span>
                    <span id="descLength" class="{{ strlen($settings['seo_home_description']) > 160 ? 'text-red-500' : 'text-green-600' }}">
                        <i class="fas fa-ruler-horizontal mr-1"></i>
                        Description: {{ strlen($settings['seo_home_description']) }}/160 characters
                    </span>
                </div>
            </div>

            <!-- Home Page SEO -->
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-home text-rose-500 mr-2"></i>
                    Home Page SEO
                </h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Page Title
                        <span class="text-gray-400 font-normal">(Recommended: 50-60 characters)</span>
                    </label>
                    <input type="text" name="seo_home_title" id="seo_home_title"
                        value="{{ $settings['seo_home_title'] }}"
                        maxlength="100"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        oninput="updatePreview()">
                    <p class="text-xs text-gray-500 mt-1">
                        This appears in search results and browser tabs.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Meta Description
                        <span class="text-gray-400 font-normal">(Recommended: 150-160 characters)</span>
                    </label>
                    <textarea name="seo_home_description" id="seo_home_description" rows="3"
                        maxlength="300"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 resize-none"
                        oninput="updatePreview()">{{ $settings['seo_home_description'] }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        A brief summary that appears under your page title in search results.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Meta Keywords
                        <span class="text-gray-400 font-normal">(Separate with commas)</span>
                    </label>
                    <textarea name="seo_home_keywords" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 resize-none"
                        placeholder="cosmetics, beauty, skincare, makeup...">{{ $settings['seo_home_keywords'] }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Relevant keywords help search engines understand your content. (Note: Less important for modern SEO)
                    </p>
                </div>
            </div>

            <!-- Robots.txt -->
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-robot text-gray-600 mr-2"></i>
                    Robots.txt
                </h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Robots.txt Content
                    </label>
                    <textarea name="seo_robots_txt" rows="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 font-mono text-sm"
                        placeholder="User-agent: *">{{ $settings['seo_robots_txt'] }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Instructions for search engine crawlers. <a href="https://developers.google.com/search/docs/advanced/robots/intro" target="_blank" class="text-rose-600 underline">Learn more</a>
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Common robots.txt examples
                    </h4>
                    <div class="text-sm text-gray-600 space-y-2">
                        <div class="font-mono bg-white p-2 rounded border text-xs">
                            <strong>Allow all:</strong><br>
                            User-agent: *<br>
                            Allow: /<br>
                            Sitemap: https://yoursite.com/sitemap.xml
                        </div>
                        <div class="font-mono bg-white p-2 rounded border text-xs">
                            <strong>Block admin:</strong><br>
                            User-agent: *<br>
                            Disallow: /admin/<br>
                            Disallow: /api/<br>
                            Allow: /
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Tips -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-yellow-800 mb-3">
                    <i class="fas fa-lightbulb mr-1"></i>
                    SEO Best Practices
                </h4>
                <div class="grid md:grid-cols-2 gap-3 text-sm text-yellow-700">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Use unique, descriptive titles for each page</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Include target keywords naturally in content</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Write compelling meta descriptions</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Use proper heading hierarchy (H1, H2, H3)</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Optimize images with alt text</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-yellow-600 mr-2 mt-0.5"></i>
                        <span>Create and submit XML sitemaps</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700 transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Save SEO Settings
                </button>
                <a href="https://search.google.com/search-console" target="_blank" class="text-green-600 hover:underline text-sm">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    Google Search Console
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updatePreview() {
    const title = document.getElementById('seo_home_title').value;
    const description = document.getElementById('seo_home_description').value;
    
    // Update preview
    document.getElementById('previewTitle').textContent = title || 'Your Page Title';
    document.getElementById('previewDescription').textContent = description.substring(0, 160) + (description.length > 160 ? '...' : '');
    
    // Update character counts
    const titleLength = title.length;
    const descLength = description.length;
    
    const titleEl = document.getElementById('titleLength');
    const descEl = document.getElementById('descLength');
    
    titleEl.innerHTML = '<i class="fas fa-ruler-horizontal mr-1"></i>Title: ' + titleLength + '/60 characters';
    titleEl.className = titleLength > 60 ? 'text-red-500' : 'text-green-600';
    
    descEl.innerHTML = '<i class="fas fa-ruler-horizontal mr-1"></i>Description: ' + descLength + '/160 characters';
    descEl.className = descLength > 160 ? 'text-red-500' : 'text-green-600';
}
</script>
@endsection
