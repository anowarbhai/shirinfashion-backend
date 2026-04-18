<?php

use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\VolumeDiscountController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'is_admin'])->group(function () {
        // Users with action-based permissions
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index')->middleware('permission:users.view');
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show')->middleware('permission:users.view');
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
        Route::put('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
        Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.password');

        // Products routes
        Route::get('products/export', [ProductController::class, 'export'])->name('products.export')->middleware('permission:products.view');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import')->middleware('permission:products.create');

        // Product resource with action-based permissions
        Route::get('products', [ProductController::class, 'index'])->name('products.index')->middleware('permission:products.view');
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create')->middleware('permission:products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store')->middleware('permission:products.create');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show')->middleware('permission:products.view');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit')->middleware('permission:products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('permission:products.edit');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('permission:products.delete');

        Route::post('/upload/image', [UploadController::class, 'image'])->name('upload.image')->middleware('permission:products.create');
        Route::post('/upload/images', [UploadController::class, 'images'])->name('upload.images')->middleware('permission:products.create');

        // Media routes
        Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)
            ->parameters(['media' => 'media'])
            ->only(['index', 'store', 'destroy'])
            ->middleware('permission:media.view');
        Route::put('media/{media}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('media.update')->middleware('permission:media.view');
        Route::post('media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload')->middleware('permission:media.upload');
        Route::get('media/debug', [\App\Http\Controllers\Admin\MediaController::class, 'debug'])->name('media.debug')->middleware('permission:media.view');

        Route::match(['POST', 'DELETE'], 'delete-order/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy')->middleware('permission:orders.delete');
        Route::match(['POST', 'DELETE'], 'delete-orders-bulk', [\App\Http\Controllers\Admin\OrderController::class, 'bulkDelete'])->name('orders.bulk-delete')->middleware('permission:orders.delete');

        // Orders resource with action-based permissions
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index')->middleware('permission:orders.view');
        Route::get('orders/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])->name('orders.create')->middleware('permission:orders.create');
        Route::post('orders', [\App\Http\Controllers\Admin\OrderController::class, 'store'])->name('orders.store')->middleware('permission:orders.create');
        Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show')->middleware('permission:orders.view');
        Route::get('orders/{order}/edit', [\App\Http\Controllers\Admin\OrderController::class, 'edit'])->name('orders.edit')->middleware('permission:orders.edit');
        Route::put('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'update'])->name('orders.update')->middleware('permission:orders.edit');
        Route::delete('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'destroy'])->name('orders.destroy')->middleware('permission:orders.delete');
        Route::put('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status')->middleware('permission:orders.edit');
        Route::post('orders/{order}/update-rate', [\App\Http\Controllers\Admin\OrderController::class, 'updateRate'])->name('orders.update-rate')->middleware('permission:orders.edit');

        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)
            ->middleware('permission:categories.view');
        Route::get('categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create')->middleware('permission:categories.create');
        Route::post('categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store')->middleware('permission:categories.create');
        Route::get('categories/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit')->middleware('permission:categories.edit');
        Route::put('categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update')->middleware('permission:categories.edit');
        Route::delete('categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:categories.delete');

        Route::get('tags', [\App\Http\Controllers\Admin\TagController::class, 'index'])->name('tags.index')->middleware('permission:categories.view');
        Route::get('tags/create', [\App\Http\Controllers\Admin\TagController::class, 'create'])->name('tags.create')->middleware('permission:categories.create');
        Route::post('tags', [\App\Http\Controllers\Admin\TagController::class, 'store'])->name('tags.store')->middleware('permission:categories.create');
        Route::get('tags/{tag}/edit', [\App\Http\Controllers\Admin\TagController::class, 'edit'])->name('tags.edit')->middleware('permission:categories.edit');
        Route::put('tags/{tag}', [\App\Http\Controllers\Admin\TagController::class, 'update'])->name('tags.update')->middleware('permission:categories.edit');
        Route::delete('tags/{tag}', [\App\Http\Controllers\Admin\TagController::class, 'destroy'])->name('tags.destroy')->middleware('permission:categories.delete');

        Route::get('attributes', [\App\Http\Controllers\Admin\AttributeController::class, 'index'])->name('attributes.index')->middleware('permission:categories.view');
        Route::get('attributes/create', [\App\Http\Controllers\Admin\AttributeController::class, 'create'])->name('attributes.create')->middleware('permission:categories.create');
        Route::post('attributes', [\App\Http\Controllers\Admin\AttributeController::class, 'store'])->name('attributes.store')->middleware('permission:categories.create');
        Route::get('attributes/{attribute}/edit', [\App\Http\Controllers\Admin\AttributeController::class, 'edit'])->name('attributes.edit')->middleware('permission:categories.edit');
        Route::put('attributes/{attribute}', [\App\Http\Controllers\Admin\AttributeController::class, 'update'])->name('attributes.update')->middleware('permission:categories.edit');
        Route::delete('attributes/{attribute}', [\App\Http\Controllers\Admin\AttributeController::class, 'destroy'])->name('attributes.destroy')->middleware('permission:categories.delete');

        Route::get('reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index')->middleware('permission:products.view');
        Route::get('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('reviews.show')->middleware('permission:products.view');
        Route::get('reviews/{review}/edit', [\App\Http\Controllers\Admin\ReviewController::class, 'edit'])->name('reviews.edit')->middleware('permission:products.edit');
        Route::put('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'update'])->name('reviews.update')->middleware('permission:products.edit');
        Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy')->middleware('permission:products.delete');

        Route::get('brands', [\App\Http\Controllers\BrandController::class, 'index'])->name('brands.index')->middleware('permission:categories.view');
        Route::get('brands/create', [\App\Http\Controllers\BrandController::class, 'create'])->name('brands.create')->middleware('permission:categories.create');
        Route::post('brands', [\App\Http\Controllers\BrandController::class, 'store'])->name('brands.store')->middleware('permission:categories.create');
        Route::get('brands/{brand}/edit', [\App\Http\Controllers\BrandController::class, 'edit'])->name('brands.edit')->middleware('permission:categories.edit');
        Route::put('brands/{brand}', [\App\Http\Controllers\BrandController::class, 'update'])->name('brands.update')->middleware('permission:categories.edit');
        Route::delete('brands/{brand}', [\App\Http\Controllers\BrandController::class, 'destroy'])->name('brands.destroy')->middleware('permission:categories.delete');

        // Coupons with action-based permissions
        Route::get('coupons', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index')->middleware('permission:coupons.view');
        Route::get('coupons/create', [\App\Http\Controllers\Admin\CouponController::class, 'create'])->name('coupons.create')->middleware('permission:coupons.create');
        Route::post('coupons', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store')->middleware('permission:coupons.create');
        Route::get('coupons/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'show'])->name('coupons.show')->middleware('permission:coupons.view');
        Route::get('coupons/{coupon}/edit', [\App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('coupons.edit')->middleware('permission:coupons.edit');
        Route::put('coupons/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'update'])->name('coupons.update')->middleware('permission:coupons.edit');
        Route::delete('coupons/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy')->middleware('permission:coupons.delete');

        // Sliders with action-based permissions
        Route::get('sliders', [\App\Http\Controllers\Admin\SliderController::class, 'index'])->name('sliders.index')->middleware('permission:sliders.view');
        Route::get('sliders/create', [\App\Http\Controllers\Admin\SliderController::class, 'create'])->name('sliders.create')->middleware('permission:sliders.create');
        Route::post('sliders', [\App\Http\Controllers\Admin\SliderController::class, 'store'])->name('sliders.store')->middleware('permission:sliders.create');
        Route::get('sliders/{slider}', [\App\Http\Controllers\Admin\SliderController::class, 'show'])->name('sliders.show')->middleware('permission:sliders.view');
        Route::get('sliders/{slider}/edit', [\App\Http\Controllers\Admin\SliderController::class, 'edit'])->name('sliders.edit')->middleware('permission:sliders.edit');
        Route::put('sliders/{slider}', [\App\Http\Controllers\Admin\SliderController::class, 'update'])->name('sliders.update')->middleware('permission:sliders.edit');
        Route::delete('sliders/{slider}', [\App\Http\Controllers\Admin\SliderController::class, 'destroy'])->name('sliders.destroy')->middleware('permission:sliders.delete');
        Route::patch('sliders/{slider}/toggle', [\App\Http\Controllers\Admin\SliderController::class, 'toggleStatus'])->name('sliders.toggle')->middleware('permission:sliders.edit');
        // Page Builder Routes - put these BEFORE resource to take precedence
        Route::get('pages/{page}/builder', [\App\Http\Controllers\Admin\PageController::class, 'builder'])->name('pages.builder')->middleware('permission:pages.edit');
        Route::post('pages/{page}/builder', [\App\Http\Controllers\Admin\PageController::class, 'builderUpdate'])->name('pages.builder-update')->middleware('permission:pages.edit');

        // Check slug routes - specific routes need to come first
        Route::get('pages/check-slug', [\App\Http\Controllers\Admin\PageController::class, 'checkSlug'])->name('pages.check-slug')->middleware('permission:pages.view');
        Route::get('pages/{page}/check-slug', [\App\Http\Controllers\Admin\PageController::class, 'checkSlug'])->name('pages.check-slug-with-id')->middleware('permission:pages.view');

        // Pages with action-based permissions
        Route::get('pages/check-slug', [\App\Http\Controllers\Admin\PageController::class, 'checkSlug'])->name('pages.check-slug')->middleware('permission:pages.view');
        Route::get('pages/{page}/check-slug', [\App\Http\Controllers\Admin\PageController::class, 'checkSlug'])->name('pages.check-slug-with-id')->middleware('permission:pages.view');
        Route::get('pages/{page}/builder', [\App\Http\Controllers\Admin\PageController::class, 'builder'])->name('pages.builder')->middleware('permission:pages.edit');
        Route::post('pages/{page}/builder', [\App\Http\Controllers\Admin\PageController::class, 'builderUpdate'])->name('pages.builder-update')->middleware('permission:pages.edit');

        Route::get('pages', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('pages.index')->middleware('permission:pages.view');
        Route::get('pages/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('pages.create')->middleware('permission:pages.edit');
        Route::post('pages', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('pages.store')->middleware('permission:pages.edit');
        Route::get('pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'show'])->name('pages.show')->middleware('permission:pages.view');
        Route::get('pages/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('pages.edit')->middleware('permission:pages.edit');
        Route::put('pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('pages.update')->middleware('permission:pages.edit');
        Route::delete('pages/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('pages.destroy')->middleware('permission:pages.edit');

        // Role & Permission routes - protected by permission middleware
        Route::middleware('permission:roles.view')->group(function () {
            Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
            Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
            Route::post('roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
            Route::get('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'show'])->name('roles.show');
            Route::get('roles/{role}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
            Route::delete('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');

            Route::get('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.index');
            Route::get('permissions/create', [\App\Http\Controllers\Admin\PermissionController::class, 'create'])->name('permissions.create');
            Route::post('permissions', [\App\Http\Controllers\Admin\PermissionController::class, 'store'])->name('permissions.store');
            Route::get('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'show'])->name('permissions.show');
            Route::get('permissions/{permission}/edit', [\App\Http\Controllers\Admin\PermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('permissions.update');
            Route::delete('permissions/{permission}', [\App\Http\Controllers\Admin\PermissionController::class, 'destroy'])->name('permissions.destroy');
        });

        // Customers with action-based permissions
        Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index')->middleware('permission:customers.view');
        Route::get('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show')->middleware('permission:customers.view');
        Route::get('customers/{customer}/edit', [\App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:customers.edit');
        Route::put('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('customers.update')->middleware('permission:customers.edit');
        Route::delete('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:customers.delete');

        Route::get('marketing/facebook', [\App\Http\Controllers\Admin\MarketingController::class, 'facebook'])->name('marketing.facebook');
        Route::post('marketing/facebook', [\App\Http\Controllers\Admin\MarketingController::class, 'facebookUpdate'])->name('marketing.facebook.update');
        Route::get('marketing/google', [\App\Http\Controllers\Admin\MarketingController::class, 'google'])->name('marketing.google');
        Route::post('marketing/google', [\App\Http\Controllers\Admin\MarketingController::class, 'googleUpdate'])->name('marketing.google.update');
        Route::get('marketing/seo', [\App\Http\Controllers\Admin\MarketingController::class, 'seo'])->name('marketing.seo');
        Route::post('marketing/seo', [\App\Http\Controllers\Admin\MarketingController::class, 'seoUpdate'])->name('marketing.seo.update');

        Route::get('settings/product', [SettingsController::class, 'productSettings'])->name('settings.product')->middleware('permission:settings.view');
        Route::post('settings/product', [SettingsController::class, 'productSettingsUpdate'])->name('settings.product.update')->middleware('permission:settings.edit');
        Route::post('settings/product/shipping', [SettingsController::class, 'shippingMethodStore'])->name('settings.product.shipping.store')->middleware('permission:settings.edit');
        Route::put('settings/product/shipping/{method}', [SettingsController::class, 'shippingMethodUpdate'])->name('settings.product.shipping.update')->middleware('permission:settings.edit');
        Route::delete('settings/product/shipping/{method}', [SettingsController::class, 'shippingMethodDestroy'])->name('settings.product.shipping.destroy')->middleware('permission:settings.edit');
        Route::post('settings/product/shipping/{method}/toggle', [SettingsController::class, 'shippingMethodToggle'])->name('settings.product.shipping.toggle')->middleware('permission:settings.edit');
        Route::post('settings/product/shipping-settings', [SettingsController::class, 'shippingSettingsUpdate'])->name('settings.product.shipping-settings.update')->middleware('permission:settings.edit');
        Route::post('settings/product/tax-settings', [SettingsController::class, 'taxSettingsUpdate'])->name('settings.product.tax-settings.update')->middleware('permission:settings.edit');
        Route::post('settings/product/contact', [SettingsController::class, 'contactSettingsUpdate'])->name('settings.product.contact.update')->middleware('permission:settings.edit');

        Route::get('settings/general', [SettingsController::class, 'generalSettings'])->name('settings.general')->middleware('permission:settings.general.view');
        Route::post('settings/general', [SettingsController::class, 'generalSettingsUpdate'])->name('settings.general.update')->middleware('permission:settings.general.edit');

        Route::get('settings/fraud-checker', [SettingsController::class, 'fraudChecker'])->name('settings.fraud-checker')->middleware('permission:settings.fraud.view');
        Route::post('settings/fraud-checker', [SettingsController::class, 'fraudCheckerUpdate'])->name('settings.fraud-checker.update')->middleware('permission:settings.fraud.edit');
        Route::post('settings/fraud-checker/test', [SettingsController::class, 'checkPhone'])->name('settings.fraud-checker.test')->middleware('permission:settings.fraud.edit');

        Route::get('settings/shipping', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'index'])->name('settings.shipping.index')->middleware('permission:settings.view');
        Route::post('settings/shipping', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'store'])->name('settings.shipping.store')->middleware('permission:settings.edit');
        Route::put('settings/shipping/{method}', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'update'])->name('settings.shipping.update')->middleware('permission:settings.edit');
        Route::delete('settings/shipping/{method}', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'destroy'])->name('settings.shipping.destroy')->middleware('permission:settings.edit');
        Route::post('settings/shipping/{method}/toggle', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'toggleStatus'])->name('settings.shipping.toggle')->middleware('permission:settings.edit');
        Route::post('settings/shipping/settings', [\App\Http\Controllers\Admin\ShippingMethodController::class, 'updateSettings'])->name('settings.shipping.settings.update')->middleware('permission:settings.edit');

        Route::get('settings/sms', [\App\Http\Controllers\Admin\SmsController::class, 'index'])->name('sms.index')->middleware('permission:settings.sms.view');
        Route::get('settings/sms/balance', [\App\Http\Controllers\Admin\SmsController::class, 'balance'])->name('sms.balance')->middleware('permission:settings.sms.view');
        Route::put('settings/sms', [\App\Http\Controllers\Admin\SmsController::class, 'update'])->name('sms.update')->middleware('permission:settings.sms.edit');
        Route::post('settings/sms/test', [\App\Http\Controllers\Admin\SmsController::class, 'test'])->name('sms.test')->middleware('permission:settings.sms.edit');

        Route::get('themes/appearance', [\App\Http\Controllers\Admin\ThemeController::class, 'appearance'])->name('themes.appearance')->middleware('permission:themes.view');
        Route::post('themes/appearance', [\App\Http\Controllers\Admin\ThemeController::class, 'appearanceUpdate'])->name('themes.appearance.update')->middleware('permission:themes.edit');
        Route::get('themes/header', [\App\Http\Controllers\Admin\ThemeController::class, 'header'])->name('themes.header')->middleware('permission:themes.view');
        Route::post('themes/header', [\App\Http\Controllers\Admin\ThemeController::class, 'headerUpdate'])->name('themes.header.update')->middleware('permission:themes.edit');
        Route::get('themes/footer', [\App\Http\Controllers\Admin\ThemeController::class, 'footer'])->name('themes.footer')->middleware('permission:themes.view');
        Route::post('themes/footer', [\App\Http\Controllers\Admin\ThemeController::class, 'footerUpdate'])->name('themes.footer.update')->middleware('permission:themes.edit');
        Route::get('themes/menu', [\App\Http\Controllers\Admin\ThemeController::class, 'menu'])->name('themes.menu')->middleware('permission:themes.view');
        Route::post('themes/menu', [\App\Http\Controllers\Admin\ThemeController::class, 'menuUpdate'])->name('themes.menu.update')->middleware('permission:themes.edit');

        // New Dynamic Menu Manager
        Route::get('menus', [\App\Http\Controllers\Admin\MenuController::class, 'menuManage'])->name('menus.manage')->middleware('permission:themes.edit');
        Route::post('menus/save', [\App\Http\Controllers\Admin\MenuController::class, 'menuSave'])->name('menus.save')->middleware('permission:themes.edit');
        Route::get('menus/api', [\App\Http\Controllers\Admin\MenuController::class, 'menuApi'])->name('menus.api')->middleware('permission:themes.view');

        Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index')->middleware('permission:customers.view');
        Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show')->middleware('permission:customers.view');
        Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy')->middleware('permission:customers.delete');
        Route::post('contacts/{contact}/replied', [ContactController::class, 'markAsReplied'])->name('contacts.replied')->middleware('permission:customers.edit');

        // Volume Discounts
        Route::get('volume-discounts', [VolumeDiscountController::class, 'index'])->name('volume-discounts.index')->middleware('permission:volume-discounts.view');
        Route::get('volume-discounts/products', [VolumeDiscountController::class, 'index'])->name('volume-discounts')->middleware('permission:volume-discounts.view');
    });
});
