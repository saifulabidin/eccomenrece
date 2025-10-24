<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/katalog', function () {
    return view('catalog');
})->name('catalog');

Route::get('/produk/{slug}', function ($slug) {
    return view('product-detail', compact('slug'));
})->name('product.detail');