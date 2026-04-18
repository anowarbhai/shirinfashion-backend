@extends('admin.layouts.master')

@section('title', 'Google Tag Manager & Analytics')

@section('header', 'Google Tag Manager & Analytics')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fab fa-google text-red-500 mr-2"></i>
                Google Tag Manager & Analytics Integration
            </h2>
            <p class="text-gray-600 text-sm mt-1">
                Configure Google Tag Manager, Analytics, and Ads tracking for comprehensive website analytics.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.marketing.google.update') }}" class="p-6">
            @csrf
            
            <!-- Google Tag Manager -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tags text-blue-500 mr-2"></i>
                    Google Tag Manager (GTM)
                </h3>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="google_tag_manager_enabled" id="google_tag_manager_enabled" 
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                        {{ $settings['google_tag_manager_enabled'] ? 'checked' : '' }}>
                    <label for="google_tag_manager_enabled" class="ml-2 text-sm font-medium text-gray-700">
                        Enable Google Tag Manager
                    </label>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        GTM Container ID
                    </label>
                    <input type="text" name="google_tag_manager_id" 
                        value="{{ $settings['google_tag_manager_id'] }}"
                        placeholder="e.g., GTM-XXXXXXX"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Format: GTM-XXXXXXX. Find it in your GTM dashboard.
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                        What can you track with GTM?
                    </h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-2 text-xs"></i>E-commerce transactions</li>
                        <li><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Button clicks & form submissions</li>
                        <li><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Scroll depth & user engagement</li>
                        <li><i class="fas fa-check text-green-500 mr-2 text-xs"></i>Custom events without code changes</li>
                    </ul>
                </div>
            </div>

            <!-- Google Analytics -->
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                    Google Analytics (GA4)
                </h3>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="google_analytics_enabled" id="google_analytics_enabled" 
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                        {{ $settings['google_analytics_enabled'] ? 'checked' : '' }}>
                    <label for="google_analytics_enabled" class="ml-2 text-sm font-medium text-gray-700">
                        Enable Google Analytics 4
                    </label>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        GA4 Measurement ID
                    </label>
                    <input type="text" name="google_analytics_id" 
                        value="{{ $settings['google_analytics_id'] }}"
                        placeholder="e.g., G-XXXXXXXXXX"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Format: G-XXXXXXXXXX. Find it in GA4 Admin > Data Streams.
                    </p>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-orange-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        GA4 Features
                    </h4>
                    <ul class="text-sm text-orange-700 space-y-1">
                        <li><i class="fas fa-arrow-right mr-2 text-xs"></i>Real-time user tracking</li>
                        <li><i class="fas fa-arrow-right mr-2 text-xs"></i>E-commerce purchase funnel</li>
                        <li><i class="fas fa-arrow-right mr-2 text-xs"></i>Audience demographics & interests</li>
                        <li><i class="fas fa-arrow-right mr-2 text-xs"></i>Custom reports & explorations</li>
                    </ul>
                </div>
            </div>

            <!-- Google Ads -->
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-ad text-yellow-500 mr-2"></i>
                    Google Ads Conversion Tracking
                </h3>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="google_ads_enabled" id="google_ads_enabled" 
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                        {{ $settings['google_ads_enabled'] ? 'checked' : '' }}>
                    <label for="google_ads_enabled" class="ml-2 text-sm font-medium text-gray-700">
                        Enable Google Ads Conversion Tracking
                    </label>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Conversion ID
                    </label>
                    <input type="text" name="google_ads_id" 
                        value="{{ $settings['google_ads_id'] }}"
                        placeholder="e.g., AW-XXXXXXXXX"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Your Google Ads conversion ID for tracking purchases.
                    </p>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-green-800 mb-2">
                    <i class="fas fa-graduation-cap mr-1"></i>
                    Quick Setup Guide
                </h4>
                <div class="grid md:grid-cols-3 gap-4 text-sm text-green-700">
                    <div>
                        <strong class="block mb-1">1. GTM Setup</strong>
                        <a href="https://tagmanager.google.com/" target="_blank" class="underline">Create container</a> → Copy Container ID
                    </div>
                    <div>
                        <strong class="block mb-1">2. GA4 Setup</strong>
                        <a href="https://analytics.google.com/" target="_blank" class="underline">Create property</a> → Get Measurement ID
                    </div>
                    <div>
                        <strong class="block mb-1">3. Google Ads</strong>
                        <a href="https://ads.google.com/" target="_blank" class="underline">Create account</a> → Get Conversion ID
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Save Settings
                </button>
                <a href="https://support.google.com/tagmanager/answer/6102821" target="_blank" class="text-blue-600 hover:underline text-sm">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    GTM Help Center
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
