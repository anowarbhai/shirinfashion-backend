<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\VolumeDiscountController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/otp-login', [AuthController::class, 'otpLogin']);
    Route::post('/otp-register', [AuthController::class, 'otpRegister']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/avatar', [AuthController::class, 'updateAvatar']);
        Route::post('/link-orders', [AuthController::class, 'linkOrders']);
        Route::post('/set-password', [AuthController::class, 'setPassword']);

        Route::get('/addresses', [AuthController::class, 'getAddresses']);
        Route::post('/addresses', [AuthController::class, 'saveAddress']);
        Route::put('/addresses/{id}', [AuthController::class, 'updateAddress']);
        Route::delete('/addresses/{id}', [AuthController::class, 'deleteAddress']);
        Route::post('/addresses/{id}/default', [AuthController::class, 'setDefaultAddress']);
    });
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

Route::get('/brands', [BrandController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/latest', [ProductController::class, 'latest']);
Route::get('/products/related/{slug}', [ProductController::class, 'related']);
Route::get('/products/settings', [ProductController::class, 'settings']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'productReviews']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Public review submission - allows guests to submit reviews
Route::post('/reviews', [ReviewController::class, 'store']);

// Coupon validation
Route::post('/coupons/validate', [CouponController::class, 'validate']);

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart', [CartController::class, 'store']);
Route::put('/cart/{cart}', [CartController::class, 'update']);
Route::delete('/cart/{cart}', [CartController::class, 'destroy']);
Route::delete('/cart', [CartController::class, 'clear']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});

Route::post('/orders/guest', [OrderController::class, 'store']);
Route::post('/orders/incomplete', [OrderController::class, 'saveIncomplete']);

Route::get('/shipping', [ShippingController::class, 'index']);
Route::post('/shipping/calculate', [ShippingController::class, 'calculateShipping']);

// Theme settings
Route::get('/theme', [ThemeController::class, 'index']);
Route::get('/favicon', [ThemeController::class, 'favicon']);

// Sliders
Route::get('/sliders', [SliderController::class, 'index']);

// Contact form
Route::post('/contact', [ContactController::class, 'store']);

// OTP Routes
Route::post('/otp/send', [OtpController::class, 'send']);
Route::post('/otp/verify', [OtpController::class, 'verify']);
Route::get('/otp/check-required', [OtpController::class, 'checkRequired']);

// Pages
Route::get('/pages', [PageController::class, 'index']);
Route::get('/pages/{slug}', [PageController::class, 'show']);

// Menus
Route::get('/menus', [App\Http\Controllers\Api\MenuController::class, 'index']);

// Volume Discounts
Route::get('/volume-discounts', [VolumeDiscountController::class, 'index']);
Route::post('/volume-discounts', [VolumeDiscountController::class, 'store']);
