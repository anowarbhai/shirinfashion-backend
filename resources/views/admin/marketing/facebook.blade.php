@extends('admin.layouts.master')

@section('title', 'Facebook Conversion API')

@section('header', 'Facebook Conversion API')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                <i class="fab fa-facebook text-blue-600 mr-2"></i>
                Facebook Pixel & Conversion API Settings
            </h2>
            <p class="text-gray-600 text-sm mt-1">
                Configure Facebook Pixel tracking and Conversion API for advanced analytics and ad optimization.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.marketing.facebook.update') }}" class="p-6">
            @csrf
            
            <!-- Facebook Pixel -->
            <div class="mb-8">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-puzzle-piece text-blue-500 mr-2"></i>
                    Facebook Pixel
                </h3>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="facebook_pixel_enabled" id="facebook_pixel_enabled" 
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                        {{ $settings['facebook_pixel_enabled'] ? 'checked' : '' }}>
                    <label for="facebook_pixel_enabled" class="ml-2 text-sm font-medium text-gray-700">
                        Enable Facebook Pixel Tracking
                    </label>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Facebook Pixel ID
                    </label>
                    <input type="text" name="facebook_pixel_id" 
                        value="{{ $settings['facebook_pixel_id'] }}"
                        placeholder="e.g., 123456789012345"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Find your Pixel ID in Facebook Events Manager
                    </p>
                </div>
            </div>

            <!-- Conversion API -->
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-md font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-server text-indigo-500 mr-2"></i>
                    Facebook Conversion API (CAPI)
                </h3>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="facebook_conversion_api_enabled" id="facebook_conversion_api_enabled" 
                        class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500" 
                        {{ $settings['facebook_conversion_api_enabled'] ? 'checked' : '' }}>
                    <label for="facebook_conversion_api_enabled" class="ml-2 text-sm font-medium text-gray-700">
                        Enable Conversion API
                    </label>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Access Token
                    </label>
                    <textarea name="facebook_access_token" rows="3"
                        placeholder="Paste your Facebook Conversion API access token here..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm">{{ $settings['facebook_access_token'] }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Generate access token from Facebook Events Manager > Settings > Conversions API
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Test Event Code (Optional)
                    </label>
                    <input type="text" name="facebook_test_event_code" 
                        value="{{ $settings['facebook_test_event_code'] }}"
                        placeholder="e.g., TEST12345"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        Use this for testing events before going live
                    </p>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="text-sm font-medium text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    How to get these credentials?
                </h4>
                <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                    <li>Go to <a href="https://business.facebook.com/events_manager" target="_blank" class="underline">Facebook Events Manager</a></li>
                    <li>Select or create your data source</li>
                    <li>For Pixel ID: Go to Settings > Pixel ID</li>
                    <li>For Access Token: Go to Settings > Conversions API > Generate access token</li>
                </ol>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Save Settings
                </button>
                <a href="https://developers.facebook.com/docs/marketing-api/conversions-api/" target="_blank" class="text-blue-600 hover:underline text-sm">
                    <i class="fas fa-external-link-alt mr-1"></i>
                    Documentation
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
