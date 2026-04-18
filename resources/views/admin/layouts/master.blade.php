<?php
use App\Models\ThemeSetting;
use Illuminate\Support\Facades\Auth;

$currentUrl = request()->url();
$user = Auth::user();

// Load roles for permission checks
if ($user) {
    $user->load('roles.permissions');
}

// Check permissions - handles null user gracefully
$canProducts = $user && ($user->isSuperAdmin() || $user->hasPermission('products.view'));
$canOrders = $user && ($user->isSuperAdmin() || $user->hasPermission('orders.view'));
$canCustomers = $user && ($user->isSuperAdmin() || $user->hasPermission('customers.view'));
$canMarketing = $user && ($user->isSuperAdmin() || $user->hasPermission('marketing.view'));
$canCoupons = $user && ($user->isSuperAdmin() || $user->hasPermission('coupons.view'));
$canSliders = $user && ($user->isSuperAdmin() || $user->hasPermission('sliders.view'));
$canThemes = $user && ($user->isSuperAdmin() || $user->hasPermission('themes.view'));
$canUsers = $user && ($user->isSuperAdmin() || $user->hasPermission('users.view'));
$canPages = $user && ($user->isSuperAdmin() || $user->hasPermission('pages.view'));
$canSettings = $user && ($user->isSuperAdmin() || $user->hasPermission('settings.view'));
$canGeneralSettings = $user && ($user->isSuperAdmin() || $user->hasPermission('settings.general.view'));
$canFraudSettings = $user && ($user->isSuperAdmin() || $user->hasPermission('settings.fraud.view'));
$canSmsSettings = $user && ($user->isSuperAdmin() || $user->hasPermission('settings.sms.view'));
$canRoles = $user && ($user->isSuperAdmin() || $user->hasPermission('roles.view'));

$isProductsMenu = str_contains($currentUrl, '/admin/products') || str_contains($currentUrl, '/admin/categories') || str_contains($currentUrl, '/admin/tags') || str_contains($currentUrl, '/admin/attributes') || str_contains($currentUrl, '/admin/reviews') || str_contains($currentUrl, '/admin/brands') || str_contains($currentUrl, '/admin/settings/product') || str_contains($currentUrl, '/admin/volume-discounts');
$isSettingsMenu = (str_contains($currentUrl, '/admin/settings') && ! str_contains($currentUrl, '/admin/settings/product') && ! str_contains($currentUrl, '/admin/settings/shipping')) || str_contains($currentUrl, '/admin/roles') || str_contains($currentUrl, '/admin/permissions');
$isThemesMenu = str_contains($currentUrl, '/admin/themes');
$isMarketingMenu = str_contains($currentUrl, '/admin/marketing') || str_contains($currentUrl, '/admin/coupons');
$isSmsMenu = str_contains($currentUrl, '/admin/settings/sms');

// Get dynamic favicon from theme settings
$themeSettings = ThemeSetting::getSettings();
$faviconUrl = $themeSettings->favicon ? asset('storage/'.$themeSettings->favicon) : asset('favicon.ico');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Shirin Fashion</title>
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <!-- Mobile Overlay (for clicking outside to close) -->
    <div id="mobile-overlay" onclick="toggleMobileMenu()" class="md:hidden fixed inset-0 bg-black/50 z-[40] hidden cursor-pointer"></div>

    <div class="flex min-h-screen">
        <aside id="sidebar" class="w-64 bg-gray-800 text-white fixed h-full flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-50">
            <!-- Mobile Close Button -->
            <button onclick="toggleMobileMenu()" class="md:hidden absolute top-4 right-4 text-gray-400 hover:text-white p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            <div class="p-6 flex-shrink-0 pt-12 md:pt-6">
                <h1 class="text-2xl font-serif font-bold text-rose-400">SHIRIN</h1>
                <p class="text-gray-400 text-sm">Admin Panel</p>
            </div>
            
            <nav class="flex-1 overflow-y-auto mt-6 pb-20">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->is('admin/dashboard') ? 'bg-gray-700 text-rose-400 border-l-4 border-rose-400' : '' }}">
                    <i class="fas fa-tachometer-alt w-6"></i>
                    <span>Dashboard</span>
                </a>

                @if($canProducts)
                <div class="relative">
                    <button onclick="toggleMenu('products-menu')" class="flex items-center w-full px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ $isProductsMenu ? 'bg-gray-700 text-rose-400' : '' }}">
                        <i class="fas fa-box w-6"></i>
                        <span>Products</span>
                        <i class="fas fa-chevron-down ml-auto text-xs transition-transform {{ $isProductsMenu ? 'rotate-180' : '' }}" id="products-menu-arrow"></i>
                    </button>
                    <div id="products-menu" class="{{ $isProductsMenu ? '' : 'hidden' }}">
                        <a href="{{ route('admin.products.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/products') && !str_contains($currentUrl, '/admin/products/') ? 'text-rose-400 bg-gray-600' : '' }}">
                            All Products
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/categories') ? 'text-rose-400 bg-gray-600' : '' }}">
                            Categories
                        </a>
                        <a href="{{ route('admin.tags.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/tags') ? 'text-rose-400 bg-gray-600' : '' }}">
                            Tags
                        </a>
                        <a href="{{ route('admin.attributes.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/attributes') ? 'text-rose-400 bg-gray-600' : '' }}">
                            Attributes
                        </a>
                        <a href="{{ route('admin.reviews.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/reviews') ? 'text-rose-400 bg-gray-600' : '' }}">
                            Reviews
                        </a>
                        <a href="{{ route('admin.brands.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/brands') ? 'text-rose-400 bg-gray-600' : '' }}">
                            Brands
                        </a>
                        <a href="{{ route('admin.settings.product') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/settings/product') ? 'text-rose-400 bg-gray-600' : '' }}">
                            Product Settings
                        </a>
                        <a href="{{ route('admin.volume-discounts.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/volume-discounts') ? 'text-rose-400 bg-gray-600' : '' }}">
                            Volume Discounts
                        </a>
                        <a href="{{ route('admin.media.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/media') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-images mr-2"></i> Media Library
                        </a>
                    </div>
                </div>
                @endif
                
                @if($canOrders)
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->is('admin/orders*') ? 'bg-gray-700 text-rose-400 border-l-4 border-rose-400' : '' }}">
                    <i class="fas fa-shopping-cart w-6"></i>
                    <span>Orders</span>
                </a>
                @endif

                @if($canCustomers)
                <a href="{{ route('admin.customers.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->is('admin/customers*') ? 'bg-gray-700 text-rose-400 border-l-4 border-rose-400' : '' }}">
                    <i class="fas fa-users w-6"></i>
                    <span>Customers</span>
                </a>
                @endif

                @if($canMarketing || $canCoupons)
                <div class="relative">
                    <button onclick="toggleMenu('marketing-menu')" class="flex items-center w-full px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ $isMarketingMenu ? 'bg-gray-700 text-rose-400' : '' }}">
                        <i class="fas fa-bullhorn w-6"></i>
                        <span>Marketing</span>
                        <i class="fas fa-chevron-down ml-auto text-xs transition-transform {{ $isMarketingMenu ? 'rotate-180' : '' }}" id="marketing-menu-arrow"></i>
                    </button>
                    <div id="marketing-menu" class="{{ $isMarketingMenu ? '' : 'hidden' }}">
                        @if($user && ($user->isSuperAdmin() || $user->hasPermission('marketing.view')))
                        <a href="{{ route('admin.marketing.facebook') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/marketing/facebook') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fab fa-facebook w-4 mr-2"></i>
                            Facebook
                        </a>
                        <a href="{{ route('admin.marketing.google') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/marketing/google') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fab fa-google w-4 mr-2"></i>
                            Google
                        </a>
                        <a href="{{ route('admin.marketing.seo') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/marketing/seo') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-search w-4 mr-2"></i>
                            SEO
                        </a>
                        @endif
                        @if($canCoupons)
                        <a href="{{ route('admin.coupons.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/coupons') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-tag w-4 mr-2"></i>
                            Coupons
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                
                @if($canUsers)
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->is('admin/users*') ? 'bg-gray-700 text-rose-400 border-l-4 border-rose-400' : '' }}">
                    <i class="fas fa-user-cog w-6"></i>
                    <span>Users</span>
                </a>
                @endif
                
                @if($canSliders)
                <a href="{{ route('admin.sliders.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->is('admin/sliders*') ? 'bg-gray-700 text-rose-400 border-l-4 border-rose-400' : '' }}">
                    <i class="fas fa-images w-6"></i>
                    <span>Hero Sliders</span>
                </a>
                @endif
                
                <a href="{{ route('admin.contacts.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->is('admin/contacts*') ? 'bg-gray-700 text-rose-400 border-l-4 border-rose-400' : '' }}">
                    <i class="fas fa-envelope w-6"></i>
                    <span>Contact Messages</span>
                    @php
                        $unreadCount = \App\Models\ContactMessage::where('status', 'pending')->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="ml-auto bg-rose-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>

                @if($canPages)
                <a href="{{ route('admin.pages.index') }}" class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ request()->is('admin/pages*') ? 'bg-gray-700 text-rose-400 border-l-4 border-rose-400' : '' }}">
                    <i class="fas fa-file-alt w-6"></i>
                    <span>Pages</span>
                </a>
                @endif
                
                @if($canThemes)
                <div class="relative">
                    <button onclick="toggleMenu('themes-menu')" class="flex items-center w-full px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ $isThemesMenu ? 'bg-gray-700 text-rose-400' : '' }}">
                        <i class="fas fa-paint-brush w-6"></i>
                        <span>Themes</span>
                        <i class="fas fa-chevron-down ml-auto text-xs transition-transform {{ $isThemesMenu ? 'rotate-180' : '' }}" id="themes-menu-arrow"></i>
                    </button>
                    <div id="themes-menu" class="{{ $isThemesMenu ? '' : 'hidden' }}">
                        <a href="{{ route('admin.themes.appearance') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/themes/appearance') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-palette w-4 mr-2"></i>
                            Appearance
                        </a>
                        <a href="{{ route('admin.themes.header') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/themes/header') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-heading w-4 mr-2"></i>
                            Header
                        </a>
                        <a href="{{ route('admin.themes.footer') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/themes/footer') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-shoe-prints w-4 mr-2"></i>
                            Footer
                        </a>
                        <a href="{{ route('admin.themes.menu') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/themes/menu') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-bars w-4 mr-2"></i>
                            Menu
                        </a>
                    </div>
                </div>
                @endif
                
                @if($canSettings || $canGeneralSettings || $canFraudSettings || $canSmsSettings || $canRoles)
                <div class="relative">
                    <button onclick="toggleMenu('settings-menu')" class="flex items-center w-full px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white transition {{ $isSettingsMenu ? 'bg-gray-700 text-rose-400' : '' }}">
                        <i class="fas fa-cog w-6"></i>
                        <span>Settings</span>
                        <i class="fas fa-chevron-down ml-auto text-xs transition-transform {{ $isSettingsMenu ? 'rotate-180' : '' }}" id="settings-menu-arrow"></i>
                    </button>
                    <div id="settings-menu" class="{{ $isSettingsMenu ? '' : 'hidden' }}">
                        @if($user && ($user->isSuperAdmin() || $user->hasPermission('settings.general.view')))
                        <a href="{{ route('admin.settings.general') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/settings/general') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-cog w-4 mr-2"></i>
                            General
                        </a>
                        @endif
                        @if($user && ($user->isSuperAdmin() || $user->hasPermission('settings.fraud.view')))
                        <a href="{{ route('admin.settings.fraud-checker') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/settings/fraud-checker') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-shield-alt w-4 mr-2"></i>
                            Fraud Checker
                        </a>
                        @endif
                        @if($user && ($user->isSuperAdmin() || $user->hasPermission('settings.sms.view')))
                        <a href="{{ route('admin.sms.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ $isSmsMenu ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-sms w-4 mr-2"></i>
                            SMS Integration
                        </a>
                        @endif
                        @if($canRoles)
                        <a href="{{ route('admin.roles.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/roles') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-user-shield w-4 mr-2"></i>
                            Roles
                        </a>
                        @if($user && ($user->isSuperAdmin() || $user->hasPermission('roles.view')))
                        <a href="{{ route('admin.permissions.index') }}" class="flex items-center pl-12 pr-6 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-600 {{ str_contains($currentUrl, '/admin/permissions') ? 'text-rose-400 bg-gray-600' : '' }}">
                            <i class="fas fa-key w-4 mr-2"></i>
                            Permissions
                        </a>
                        @endif
                        @endif
                    </div>
                </div>
                @endif
            </nav>

            <div class="fixed bottom-0 w-64 p-4 border-t border-gray-700 bg-gray-800">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-300 hover:text-white transition">
                        <i class="fas fa-sign-out-alt w-6"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 ml-0 md:ml-64">
            <header class="bg-white shadow-sm py-4 px-4 md:px-8 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <!-- Mobile: Hamburger next to title -->
                    <button id="mobile-menu-toggle" onclick="toggleMobileMenu()" class="md:hidden p-2 bg-gray-100 rounded-lg hover:bg-gray-200 text-gray-700">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-700">@yield('header', 'Dashboard')</h2>
                </div>
                
                <div class="relative">
                    <button onclick="toggleProfileDropdown()" class="flex items-center space-x-2 hover:bg-gray-100 rounded-lg px-2 py-1 transition">
                        <span class="text-gray-600 hidden md:inline">{{ auth()->user()->name }}</span>
                        @if(auth()->user()->avatar)
                        <img src="{{ asset(auth()->user()->avatar) }}" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                        @else
                        <div class="w-10 h-10 bg-rose-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        @endif
                        <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                    </button>
                    <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200">
                        <a href="{{ route('admin.profile.show') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle w-5"></i>
                            <span>Profile</span>
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="p-4 md:p-8">
                @php
                    $successMsg = session('success');
                    $errorMsg = session('error');
                @endphp
                
                @if($successMsg)
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ $successMsg }}
                    </div>
                @endif
                
                @if($errorMsg)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $errorMsg }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    <style>
        /* Hide hamburger on desktop by default */
        #mobile-menu-toggle {
            display: none;
        }
        
        @media (max-width: 768px) {
            main {
                margin-left: 0 !important;
                margin-top: 0 !important;
                padding-top: 0 !important;
            }
            
            /* Header mobile */
            header {
                padding: 12px 16px !important;
            }
            
            header h2 {
                font-size: 18px !important;
            }
            
            #mobile-menu-toggle {
                display: flex !important;
            }
            
            /* Responsive Tables */
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            table {
                font-size: 12px;
            }
            
            table th, table td {
                padding: 8px 4px;
            }
            
            .actions {
                flex-direction: column;
                gap: 4px;
            }
            
            .actions a, .actions button {
                padding: 4px 8px;
                font-size: 11px;
            }
            
            /* Responsive Grid */
            .grid {
                gap: 12px !important;
            }
            
            /* Responsive Form */
            .form-group {
                margin-bottom: 12px;
            }
            
            input[type="text"],
            input[type="email"],
            input[type="password"],
            input[type="number"],
            select,
            textarea {
                font-size: 14px;
                padding: 8px 12px;
            }
            
            button, .btn {
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
    
    @stack('scripts')
    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(menuId + '-arrow');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                if (arrow) {
                    arrow.classList.add('rotate-180');
                }
            } else {
                menu.classList.add('hidden');
                if (arrow) {
                    arrow.classList.remove('rotate-180');
                }
            }
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profile-dropdown');
            const button = event.target.closest('button');
            if (!button && dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
