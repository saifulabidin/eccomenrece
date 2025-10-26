<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <base href="{{ url('/') }}/">
    
    <!-- Favicon -->
    @php
        $config = \App\Models\StoreConfig::first();
        $faviconUrl = $config && $config->favicon 
            ? asset('storage/' . $config->favicon) 
            : asset('favicon.ico');
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $faviconUrl }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#3b82f6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
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

    <footer class="modern-footer bg-darker text-light py-5 mt-5">
        <div class="container">
            <div class="row g-4">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-primary mb-3">{{ $storeName }}</h5>
                    @php
                        $config = \App\Models\StoreConfig::first();
                    @endphp
                    <p class="text-muted small">
                        {{ $config && $config->description 
                            ? $config->description 
                            : 'Toko online terpercaya menyediakan berbagai produk berkualitas dengan harga terbaik. Belanja mudah, aman, dan terpercaya.' 
                        }}
                    </p>
                    <div class="social-links mt-3">
                        @if($config && $config->facebook_url)
                            <a href="{{ $config->facebook_url }}" target="_blank" class="text-light me-3" title="Facebook">
                                <i class="bi bi-facebook fs-5"></i>
                            </a>
                        @endif
                        @if($config && $config->instagram_url)
                            <a href="{{ $config->instagram_url }}" target="_blank" class="text-light me-3" title="Instagram">
                                <i class="bi bi-instagram fs-5"></i>
                            </a>
                        @endif
                        @if($config && $config->whatsapp_number)
                            <a href="https://wa.me/{{ $config->whatsapp_number }}" target="_blank" class="text-light me-3" title="WhatsApp">
                                <i class="bi bi-whatsapp fs-5"></i>
                            </a>
                        @endif
                        @if($config && $config->twitter_url)
                            <a href="{{ $config->twitter_url }}" target="_blank" class="text-light" title="Twitter">
                                <i class="bi bi-twitter fs-5"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="text-light mb-3">Menu</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="{{ route('home') }}" class="text-muted small">Home</a></li>
                        <li><a href="{{ route('catalog') }}" class="text-muted small">Katalog</a></li>
                        <li><a href="{{ route('cart') }}" class="text-muted small">Keranjang</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-light mb-3">Kategori</h6>
                    <ul class="list-unstyled footer-links">
                        @php
                            $categories = \App\Models\Category::take(5)->get();
                        @endphp
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('catalog', ['category' => $category->id]) }}" class="text-muted small">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-light mb-3">Kontak</h6>
                    <ul class="list-unstyled footer-contact">
                        @php
                            $config = \App\Models\StoreConfig::first();
                        @endphp
                        @if($config && $config->email)
                            <li class="text-muted small mb-2">
                                <i class="bi bi-envelope me-2"></i>
                                {{ $config->email }}
                            </li>
                        @endif
                        @if($config && $config->phone)
                            <li class="text-muted small mb-2">
                                <i class="bi bi-telephone me-2"></i>
                                {{ $config->phone }}
                            </li>
                        @endif
                        @if($config && $config->whatsapp_number)
                            <li class="text-muted small mb-2">
                                <i class="bi bi-whatsapp me-2"></i>
                                {{ $config->whatsapp_number }}
                            </li>
                        @endif
                        @if($config && $config->address)
                            <li class="text-muted small">
                                <i class="bi bi-geo-alt me-2"></i>
                                {{ Str::limit($config->address, 50) }}
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <!-- Copyright -->
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted small mb-0">
                        &copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-muted small me-3">Privacy Policy</a>
                    <a href="#" class="text-muted small">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>