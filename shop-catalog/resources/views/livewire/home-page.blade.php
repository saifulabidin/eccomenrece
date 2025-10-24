<div class="container-fluid px-0">
    <!-- Hero Section -->
    <section class="hero-section {{ count($heroImages) > 0 ? 'hero-with-images' : 'bg-primary text-white' }} py-5">
        @if(count($heroImages) > 0)
        <!-- Hero Carousel -->
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($heroImages as $index => $image)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                    <img src="{{ asset('storage/' . $image) }}"
                         class="hero-image"
                         alt="Hero Image {{ $index + 1 }}"
                         loading="lazy"
                         style="display: block;">
                    <div class="carousel-caption d-none d-md-block">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <h1 class="display-4 fw-bold mb-4 text-white">Selamat Datang di {{ $storeName }}</h1>
                                    <p class="lead mb-4 text-white-50">Temukan produk terbaik untuk kebutuhan Anda dengan kualitas terjamin dan harga kompetitif.</p>
                                    <div class="d-flex gap-3">
                                        <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg px-4">
                                            <i class="bi bi-shop me-2"></i>Jelajahi Produk
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <!-- Fallback Hero -->
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Selamat Datang di {{ $storeName }}</h1>
                    <p class="lead mb-4">Temukan produk terbaik untuk kebutuhan Anda dengan kualitas terjamin dan harga kompetitif.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('catalog') }}" class="btn btn-light btn-lg px-4">
                            <i class="bi bi-shop me-2"></i>Jelajahi Produk
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-4 mt-lg-0">
                    <div class="hero-image">
                        <i class="bi bi-cart-check display-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </section>

    <!-- Featured Products Section -->
    <section id="featured" class="py-5 bg-light">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold text-dark mb-3">Produk Unggulan</h2>
                    <p class="text-muted lead">Koleksi produk terbaik pilihan kami</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @livewire('featured-products')
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg px-5">
                    Lihat Semua Produk <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold text-dark mb-3">Kategori Produk</h2>
                    <p class="text-muted lead">Temukan produk berdasarkan kategori favorit Anda</p>
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
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-truck display-4"></i>
                    </div>
                    <h4 class="fw-bold">Pengiriman Cepat</h4>
                    <p class="mb-0">Pengiriman ke seluruh Indonesia dengan jasa terpercaya</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-shield-check display-4"></i>
                    </div>
                    <h4 class="fw-bold">Produk Berkualitas</h4>
                    <p class="mb-0">Semua produk melalui proses quality control ketat</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-headset display-4"></i>
                    </div>
                    <h4 class="fw-bold">Customer Service</h4>
                    <p class="mb-0">Tim support siap membantu Anda 24/7</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 bg-dark text-white">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-3">Siap Berbelanja?</h2>
            <p class="lead mb-4">Mulai jelajahi katalog produk kami dan temukan yang Anda butuhkan</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-shop me-2"></i>Mulai Belanja
                </a>
                <a href="https://wa.me/{{ $storeWhatsapp }}" target="_blank" class="btn btn-success btn-lg px-5">
                    <i class="bi bi-whatsapp me-2"></i>Hubungi Kami
                </a>
            </div>
        </div>
    </section>
</div>
