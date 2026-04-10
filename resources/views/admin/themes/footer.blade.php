@extends('admin.layouts.master')

@section('title', 'Footer - Shirin Fashion Admin')
@section('header', 'Footer Settings')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm p-6">
    <p class="text-gray-600 mb-6">Choose a footer style for your website. The selected style will be applied to all pages.</p>
    
    <form method="POST" action="{{ route('admin.themes.footer.update') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($footerStyles as $key => $style)
            <label class="cursor-pointer">
                <input type="radio" name="footer_style" value="{{ $key }}" class="hidden peer" {{ $settings->footer_style == $key ? 'checked' : '' }}>
                <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-rose-500 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition-all">
                    <div class="bg-gray-100 rounded-lg h-32 mb-4 flex items-center justify-center">
                        @if(file_exists(public_path($style['preview'])))
                        <img src="{{ asset($style['preview']) }}" alt="{{ $style['name'] }}" class="h-full w-full object-cover rounded">
                        @else
                        <div class="text-center text-gray-400">
                            <i class="fas fa-image text-4xl mb-2"></i>
                            <p class="text-sm">Preview</p>
                        </div>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ $style['name'] }}</h3>
                    <p class="text-sm text-gray-600">{{ $style['description'] }}</p>
                    <div class="mt-3 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $settings->footer_style == $key ? 'bg-rose-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                            {{ $settings->footer_style == $key ? 'Selected' : 'Select' }}
                        </span>
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        <div class="mt-6 pt-6 border-t">
            <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
                <i class="fas fa-save mr-2"></i>Save Footer Style
            </button>
        </div>
    </form>
</div>

<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
    <h3 class="font-semibold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Footer Style Information</h3>
    <ul class="text-sm text-blue-800 space-y-2">
        <li><strong>Classic Footer:</strong> Four-column layout with company info, quick links, customer service, and newsletter signup.</li>
        <li><strong>Modern Footer:</strong> Two-column design with simplified navigation and social media links.</li>
        <li><strong>Minimal Footer:</strong> Clean copyright bar with essential links and social icons only.</li>
    </ul>
</div>
@endsection
