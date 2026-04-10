<?php

use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UploadController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'is_admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.password');

        Route::resource('products', ProductController::class);

        Route::post('/upload/image', [UploadController::class, 'image'])->name('upload.image');
        Route::post('/upload/images', [UploadController::class, 'images'])->name('upload.images');

        Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->parameters(['media' => 'media'])->only(['index', 'store', 'destroy']);
        Route::put('media/{media}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('media.update');
        Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
        Route::get('media/debug', [\App\Http\Controllers\Admin\MediaController::class, 'debug'])->name('media.debug');

        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
        Route::put('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('orders/bulk-delete', [\App\Http\Controllers\Admin\OrderController::class, 'bulkDelete'])->name('orders.bulk-delete');

        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('tags', \App\Http\Controllers\Admin\TagController::class);
        Route::resource('attributes', \App\Http\Controllers\Admin\AttributeController::class);
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class);
        Route::resource('brands', \App\Http\Controllers\BrandController::class);
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        Route::resource('sliders', \App\Http\Controllers\Admin\SliderController::class);
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);

        Route::get('marketing/facebook', [\App\Http\Controllers\Admin\MarketingController::class, 'facebook'])->name('marketing.facebook');
        Route::post('marketing/facebook', [\App\Http\Controllers\Admin\MarketingController::class, 'facebookUpdate'])->name('marketing.facebook.update');
        Route::get('marketing/google', [\App\Http\Controllers\Admin\MarketingController::class, 'google'])->name('marketing.google');
        Route::post('marketing/google', [\App\Http\Controllers\Admin\MarketingController::class, 'googleUpdate'])->name('marketing.google.update');
        Route::get('marketing/seo', [\App\Http\Controllers\Admin\MarketingController::class, 'seo'])->name('marketing.seo');
        Route::post('marketing/seo', [\App\Http\Controllers\Admin\MarketingController::class, 'seoUpdate'])->name('marketing.seo.update');

        Route::get('settings/product', [SettingsController::class, 'productSettings'])->name('settings.product');
        Route::post('settings/product', [SettingsController::class, 'productSettingsUpdate'])->name('settings.product.update');
        Route::post('settings/product/shipping', [SettingsController::class, 'shippingMethodStore'])->name('settings.product.shipping.store');
        Route::put('settings/product/shipping/{method}', [SettingsController::class, 'shippingMethodUpdate'])->name('settings.product.shipping.update');
        Route::delete('settings/product/shipping/{method}', [SettingsController::class, 'shippingMethodDestroy'])->name('settings.product.shipping.destroy');
        Route::post('settings/product/shipping/{method}/toggle', [SettingsController::class, 'shippingMethodToggle'])->name('settings.product.shipping.toggle');
        Route::post('settings/product/shipping-settings', [SettingsController::class, 'shippingSettingsUpdate'])->name('settings.product.shipping-settings.update');
        Route::post('settings/product/tax-settings', [SettingsController::class, 'taxSettingsUpdate'])->name('settings.product.tax-settings.update');
        Route::post('settings/product/contact', [SettingsController::class, 'contactSettingsUpdate'])->name('settings.product.contact.update');

        Route::get('settings/general', [SettingsController::class, 'generalSettings'])->name('settings.general');
        Route::post('settings/general', [SettingsController::class, 'generalSettingsUpdate'])->name('settings.general.update');

        Route::get('settings/fraud-checker', [SettingsController::class, 'fraudChecker'])->name('settings.fraud-checker');
        Route::post('settings/fraud-checker', [SettingsController::class, 'fraudCheckerUpdate'])->name('settings.fraud-checker.update');
        Route::post('settings/fraud-checker/test', [SettingsController::class, 'checkPhone'])->name('settings.fraud-checker.test');

        Route::get('settings/shipping', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'index'])->name('settings.shipping.index');
        Route::post('settings/shipping', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'store'])->name('settings.shipping.store');
        Route::put('settings/shipping/{method}', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'update'])->name('settings.shipping.update');
        Route::delete('settings/shipping/{method}', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'destroy'])->name('settings.shipping.destroy');
        Route::post('settings/shipping/{method}/toggle', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'toggleStatus'])->name('settings.shipping.toggle');
        Route::post('settings/shipping/settings', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'updateSettings'])->name('settings.shipping.settings.update');

        Route::get('settings/sms', [\App\Http\Controllers\Admin\SmsController::class, 'index'])->name('sms.index');
        Route::get('settings/sms/balance', [\App\Http\Controllers\Admin\SmsController::class, 'balance'])->name('sms.balance');
        Route::put('settings/sms', [\App\Http\Controllers\Admin\SmsController::class, 'update'])->name('sms.update');
        Route::post('settings/sms/test', [\App\Http\Controllers\Admin\SmsController::class, 'test'])->name('sms.test');

        Route::get('themes/appearance', [\App\Http\Controllers\Admin\ThemeController::class, 'appearance'])->name('themes.appearance');
        Route::post('themes/appearance', [\App\Http\Controllers\Admin\ThemeController::class, 'appearanceUpdate'])->name('themes.appearance.update');
        Route::get('themes/header', [\App\Http\Controllers\Admin\ThemeController::class, 'header'])->name('themes.header');
        Route::post('themes/header', [\App\Http\Controllers\Admin\ThemeController::class, 'headerUpdate'])->name('themes.header.update');
        Route::get('themes/footer', [\App\Http\Controllers\Admin\ThemeController::class, 'footer'])->name('themes.footer');
        Route::post('themes/footer', [\App\Http\Controllers\Admin\ThemeController::class, 'footerUpdate'])->name('themes.footer.update');
        Route::get('themes/menu', [\App\Http\Controllers\Admin\ThemeController::class, 'menu'])->name('themes.menu');
        Route::post('themes/menu', [\App\Http\Controllers\Admin\ThemeController::class, 'menuUpdate'])->name('themes.menu.update');

        // New Dynamic Menu Manager
        Route::get('menus', [\App\Http\Controllers\Admin\MenuController::class, 'menuManage'])->name('menus.manage');
        Route::post('menus/save', [\App\Http\Controllers\Admin\MenuController::class, 'menuSave'])->name('menus.save');
        Route::get('menus/api', [\App\Http\Controllers\Admin\MenuController::class, 'menuApi'])->name('menus.api');

        Route::get('pages/{page}/builder', [\App\Http\Controllers\Admin\PageController::class, 'builder'])->name('pages.builder');
        Route::post('pages/{page}/builder', [\App\Http\Controllers\Admin\PageController::class, 'builderUpdate'])->name('pages.builder.update');
        Route::patch('sliders/{slider}/toggle', [\App\Http\Controllers\Admin\SliderController::class, 'toggleStatus'])->name('sliders.toggle');

        Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
        Route::post('contacts/{contact}/replied', [ContactController::class, 'markAsReplied'])->name('contacts.replied');
    });
});
