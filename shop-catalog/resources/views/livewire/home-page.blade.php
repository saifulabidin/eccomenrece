<div class="container-fluid px-0">
    <!-- Modern Hero Section -->
    <section class="modern-hero">
        @if(count($heroImages) > 0)
        <!-- Hero Carousel with Modern Styling -->
        <div id="heroCarousel" class="carousel slide carousel-fade modern-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($heroImages as $index => $image)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <div class="hero-bg" style="background-image: url('{{ asset('storage/' . $image) }}')"></div>
                    <div class="hero-overlay"></div>
                    <div class="hero-content container">
                        <div class="row align-items-center min-vh-75">
                            <div class="col-lg-8 col-xl-7">
                                <div class="hero-text animated fadeInUp">
                                    <span class="hero-badge mb-3">Selamat Datang</span>
                                    <h1 class="hero-title">{{ $storeName }}</h1>
                                    <p class="hero-subtitle">Temukan produk terbaik untuk kebutuhan Anda dengan kualitas terjamin dan harga kompetitif.</p>
                                    <div class="hero-buttons d-flex flex-wrap gap-3">
                                        <a href="{{ route('catalog') }}" class="btn-modern btn-primary-modern">
                                            <i class="fas fa-shopping-bag me-2"></i>Jelajahi Produk
                                        </a>
                                        <a href="https://wa.me/6281234567890" target="_blank" class="btn-modern btn-outline-modern">
                                            <i class="fab fa-whatsapp me-2"></i>Konsultasi
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Carousel Controls -->
            @if(count($heroImages) > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            @endif
        </div>
        @else
        <!-- Modern Fallback Hero -->
        <div class="hero-fallback">
            <div class="hero-content container">
                <div class="row align-items-center min-vh-75">
                    <div class="col-lg-8 col-xl-7">
                        <div class="hero-text animated fadeInUp">
                            <span class="hero-badge mb-3">Selamat Datang</span>
                            <h1 class="hero-title">{{ $storeName }}</h1>
                            <p class="hero-subtitle">Temukan produk terbaik untuk kebutuhan Anda dengan kualitas terjamin dan harga kompetitif.</p>
                            <div class="hero-buttons d-flex flex-wrap gap-3">
                                <a href="{{ route('catalog') }}" class="btn-modern btn-primary-modern">
                                    <i class="fas fa-shopping-bag me-2"></i>Jelajahi Produk
                                </a>
                                <a href="https://wa.me/6281234567890" target="_blank" class="btn-modern btn-outline-modern">
                                    <i class="fab fa-whatsapp me-2"></i>Konsultasi
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-xl-5 text-center mt-4 mt-lg-0">
                        <div class="hero-illustration animated fadeInRight">
                            <div class="hero-icon-circle">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="floating-elements">
                                <div class="floating-element" style="animation-delay: 0s;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="floating-element" style="animation-delay: 0.5s;">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="floating-element" style="animation-delay: 1s;">
                                    <i class="fas fa-gift"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="modern-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <span class="section-badge">Pilihan Terbaik</span>
                    <h2 class="section-title">Produk Unggulan</h2>
                    <p class="section-subtitle">Koleksi produk terbaik pilihan kami dengan kualitas terjamin</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @livewire('featured-products')
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('catalog') }}" class="btn-modern btn-outline-modern btn-lg">
                    <i class="fas fa-th-large me-2"></i>Lihat Semua Produk
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="modern-section bg-dark-alt">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <span class="section-badge">Jelajahi</span>
                    <h2 class="section-title">Kategori Produk</h2>
                    <p class="section-subtitle">Temukan produk berdasarkan kategori favorit Anda</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @livewire('category-list')
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="modern-section features-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <h3 class="feature-title">Pengiriman Cepat</h3>
                        <p class="feature-description">Pengiriman ke seluruh Indonesia dengan jasa terpercaya</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Produk Berkualitas</h3>
                        <p class="feature-description">Semua produk melalui proses quality control ketat</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">Customer Service</h3>
                        <p class="feature-description">Tim support siap membantu Anda 24/7</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern CTA Section -->
    <section class="cta-section">
        <div class="cta-background"></div>
        <div class="cta-overlay"></div>
        <div class="container text-center position-relative">
            <div class="cta-content">
                <span class="cta-badge">Mulai Sekarang</span>
                <h2 class="cta-title">Siap Berbelanja?</h2>
                <p class="cta-subtitle">Mulai jelajahi katalog produk kami dan temukan yang Anda butuhkan</p>
                <div class="cta-buttons d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('catalog') }}" class="btn-modern btn-primary-modern btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Mulai Belanja
                    </a>
                    <a href="https://wa.me/{{ $storeConfig->whatsapp_number ?? '6282242034791' }}" target="_blank" class="btn-modern btn-whatsapp btn-lg">
                        <i class="fab fa-whatsapp me-2"></i>Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
