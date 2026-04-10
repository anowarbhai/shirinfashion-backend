@extends('admin.layouts.master')

@section('title', 'Menu Manager - Shirin Fashion Admin')
@section('header', 'Menu Manager')

@section('content')
@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow-sm p-6">
    <p class="text-gray-600 mb-6">
        <i class="fas fa-info-circle mr-2 text-rose-600"></i>
        Manage your website menus here. You can create and customize menus for header, footer, and mobile navigation.
    </p>

    <!-- Menu Locations -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.menus.manage') }}" class="block p-6 border-2 border-gray-200 rounded-xl hover:border-rose-500 hover:bg-rose-50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-bars text-3xl text-gray-400 group-hover:text-rose-500"></i>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-rose-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-rose-600">Header Menu</h3>
            <p class="text-sm text-gray-600 mt-2">Main navigation menu displayed in the website header.</p>
        </a>

        <a href="{{ route('admin.menus.manage') }}" class="block p-6 border-2 border-gray-200 rounded-xl hover:border-rose-500 hover:bg-rose-50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-th-large text-3xl text-gray-400 group-hover:text-rose-500"></i>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-rose-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-rose-600">Footer Menus</h3>
            <p class="text-sm text-gray-600 mt-2">Menus displayed in the footer columns.</p>
        </a>

        <a href="{{ route('admin.menus.manage') }}" class="block p-6 border-2 border-gray-200 rounded-xl hover:border-rose-500 hover:bg-rose-50 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <i class="fas fa-mobile-alt text-3xl text-gray-400 group-hover:text-rose-500"></i>
                <i class="fas fa-chevron-right text-gray-400 group-hover:text-rose-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-rose-600">Mobile Menu</h3>
            <p class="text-sm text-gray-600 mt-2">Menu displayed on mobile devices.</p>
        </a>
    </div>

    <!-- Direct Link to Menu Manager -->
    <div class="mt-8 p-6 bg-gradient-to-r from-rose-50 to-rose-100 rounded-xl">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <i class="fas fa-magic mr-2 text-rose-600"></i>
            Advanced Menu Editor
        </h3>
        <p class="text-gray-600 mb-4">
            Click the button below to open the full menu editor where you can add, edit, reorder, and delete menu items.
        </p>
        <a href="{{ route('admin.menus.manage') }}" class="inline-flex items-center px-6 py-3 bg-rose-600 text-white rounded-lg hover:bg-rose-700 font-medium">
            <i class="fas fa-external-link-alt mr-2"></i>
            Open Menu Manager
        </a>
    </div>

    <!-- Quick Info -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="font-semibold text-blue-900 mb-2">
                <i class="fas fa-plus-circle mr-2"></i>How to Add Menu Items
            </h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li>1. Click "Open Menu Manager" above</li>
                <li>2. Select a menu tab (Footer, Header, or Mobile)</li>
                <li>3. Click "Add Item" or use Quick Links</li>
                <li>4. Edit title and URL as needed</li>
                <li>5. Click "Save Menu"</li>
            </ul>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-6">
            <h3 class="font-semibold text-green-900 mb-2">
                <i class="fas fa-check-circle mr-2"></i>Available Links
            </h3>
            <ul class="text-sm text-green-800 space-y-1">
                <li><code>/</code> - Home page</li>
                <li><code>/shop</code> - Shop page</li>
                <li><code>/categories</code> - Categories</li>
                <li><code>/faq</code> - FAQ</li>
                <li><code>/shipping</code> - Shipping Info</li>
                <li><code>/returns</code> - Returns</li>
                <li><code>/privacy-policy</code> - Privacy Policy</li>
            </ul>
        </div>
    </div>

    <!-- Menu API Status -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <h4 class="font-medium text-gray-700 mb-2">Menu API Status</h4>
        <p class="text-sm text-gray-600">
            Menu data is available at: 
            <code class="bg-gray-200 px-2 py-1 rounded text-rose-600">/api/menus</code>
        </p>
    </div>
</div>
@endsection