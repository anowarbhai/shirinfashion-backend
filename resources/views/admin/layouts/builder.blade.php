@php
    $currentUrl = request()->url();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Page Builder') - Admin</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.9.1/dist/themes/classic.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.9.1/dist/pickr.min.js"></script>
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <style>
        .pcr-app { z-index: 99999 !important; }
        .ql-container { min-height: 150px; font-size: 14px; }
        .ql-editor { min-height: 150px; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 m-0 p-0">
    <!-- Admin Header -->
    <header class="bg-gray-800 text-white py-4 px-6 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.dashboard') }}" class="text-rose-400 hover:text-rose-300">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
            <h1 class="text-xl font-semibold">Page Builder</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-gray-400">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-white">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </header>
    
    @yield('content')
    @stack('scripts')
</body>
</html>
