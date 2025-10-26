<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/katalog', function () {
    return view('catalog');
})->name('catalog');

Route::get('/produk/{slug}', function ($slug) {
    $product = \App\Models\Product::where('slug', $slug)->firstOrFail();
    return view('product-detail', compact('slug', 'product'));
})->name('product.detail');

Route::get('/keranjang', function () {
    return view('cart');
})->name('cart');

// Sitemap XML
Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// PWA Manifest JSON
Route::get('/manifest.json', [App\Http\Controllers\ManifestController::class, 'index'])->name('manifest');

// Google Authentication Routes
Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/auth/google/logout', [App\Http\Controllers\Auth\GoogleController::class, 'logout'])->name('auth.google.logout');

// Admin Google Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', function () {
        return view('auth.admin-login');
    })->name('login');

    Route::get('/auth/google', [App\Http\Controllers\Auth\AdminGoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [App\Http\Controllers\Auth\AdminGoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Filament logout route
Route::post('/admin/logout', [App\Http\Controllers\Auth\AdminGoogleController::class, 'logout'])
    ->name('filament.admin.auth.logout');

// Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/reviews/export', [App\Http\Controllers\ReviewExportController::class, 'export'])->name('reviews.export');
});
