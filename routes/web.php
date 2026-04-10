<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/api');
});

Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/login', function () {
    return redirect('/api/login');
})->name('login');

require __DIR__.'/admin.php';
