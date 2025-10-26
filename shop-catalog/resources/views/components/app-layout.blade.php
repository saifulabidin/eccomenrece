<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{ url('/') }}/">
    
    <!-- Title -->
    <title>{{ $metaTitle ?? $storeName }}</title>
    
    <!-- Standard Meta Tags -->
    <meta name="description" content="{{ $metaDescription ?? 'Toko Online Terpercaya' }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
    <meta property="og:title" content="{{ $ogTitle ?? $metaTitle ?? $storeName }}">
    <meta property="og:description" content="{{ $ogDescription ?? $metaDescription ?? 'Toko Online Terpercaya' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('storage/' . $storeLogo) }}">
    <meta property="og:site_name" content="{{ $storeName }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $twitterUrl ?? url()->current() }}">
    <meta name="twitter:title" content="{{ $twitterTitle ?? $ogTitle ?? $metaTitle ?? $storeName }}">
    <meta name="twitter:description" content="{{ $twitterDescription ?? $ogDescription ?? $metaDescription ?? 'Toko Online Terpercaya' }}">
    <meta name="twitter:image" content="{{ $twitterImage ?? $ogImage ?? asset('storage/' . $storeLogo) }}">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-dark text-light">
    <nav class="navbar navbar-expand-lg navbar-dark modern-nav sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <!-- Logo/Brand -->
            @if($storeLogo)
                <a class="navbar-brand modern-brand d-flex align-items-center" href="{{ route('home') }}">
                    <div class="brand-logo">
                        <img src="{{ asset('storage/' . $storeLogo) }}" alt="{{ $storeName }}" class="brand-img">
                    </div>
                    <span class="brand-name">{{ $storeName }}</span>
                </a>
            @else
                <a class="navbar-brand modern-brand" href="{{ route('home') }}">
                    <i class="fas fa-store brand-icon"></i>
                    <span class="brand-name">{{ $storeName }}</span>
                </a>
            @endif

            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler modern-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <div class="toggler-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <!-- Desktop Navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto nav-menu">
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home nav-icon"></i>
                            <span class="nav-text">Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-modern {{ request()->is('katalog*') ? 'active' : '' }}" href="{{ route('catalog') }}">
                            <i class="fas fa-th-large nav-icon"></i>
                            <span class="nav-text">Katalog</span>
                        </a>
                    </li>
                </ul>

                <!-- Cart Button -->
                <div class="cart-nav">
                    @livewire('shopping-cart')
                </div>
            </div>

            <!-- Mobile Cart Button -->
            <div class="cart-mobile d-lg-none">
            </div>
        </div>
    </nav>

    <main>
        {{ $slot }}
    </main>

    <footer class="py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 {{ $storeName }}. All rights reserved.</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>