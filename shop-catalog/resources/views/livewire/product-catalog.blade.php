<div class="container-fluid py-4 catalog-page">
    <!-- Header Section -->
    <div class="row mb-8">
        <div class="col-12">
            <div class="text-center mb-8">
                <h1 class="display-8 fw-bold text-light mb-2">
                    <i class="bi bi-shop text-primary me-3"></i>Katalog Produk
                </h1>
                <p class="text-muted lead">Temukan produk berkualitas dengan harga terbaik</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="search-filter-section">
                <!-- Desktop Layout -->
                <div class="d-none d-lg-block">
                    <div class="row g-3 align-items-center">
                        <!-- Search Input -->
                        <div class="col-lg-8">
                            <div class="search-box">
                                <div class="input-group search-input-group">
                                    <span class="input-group-text bg-dark border-secondary">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control bg-dark border-secondary text-light search-input"
                                           placeholder="Cari produk..."
                                           wire:model.live="search">
                                    @if($search)
                                        <button class="btn btn-outline-secondary border-start-0 clear-search" type="button" wire:click="$set('search', '')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </div>
                                @if($search)
                                    <div class="search-indicator mt-1">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Pencarian: <span class="text-primary">{{ $search }}</span>
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-lg-4">
                            <div class="category-filter">
                                <div class="category-select-wrapper">
                                    <select class="form-select form-select-sm bg-dark border-secondary text-light category-select"
                                            wire:model.live="categoryId">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="bi bi-funnel category-icon"></i>
                                </div>
                                @if($categoryId)
                                    <div class="filter-indicator mt-1">
                                        <small class="text-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            {{ $categories->firstWhere('id', $categoryId)->name ?? '' }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Layout -->
                <div class="d-lg-none">
                    <div class="mobile-search-section">
                        <!-- Search Bar -->
                        <div class="mobile-search-box mb-3">
                            <div class="input-group search-input-group">
                                <span class="input-group-text bg-dark border-secondary">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text"
                                       class="form-control bg-dark border-secondary text-light search-input"
                                       placeholder="Cari produk..."
                                       wire:model.live="search">
                                @if($search)
                                    <button class="btn btn-outline-secondary border-start-0 clear-search" type="button" wire:click="$set('search', '')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Mobile Filter Toggle -->
                        <div class="mobile-filter-toggle">
                            <button class="btn btn-outline-secondary w-100" wire:click="toggleMobileFilter">
                                <i class="bi bi-funnel me-2"></i>
                                Filter Kategori
                                @if($categoryId)
                                    <span class="badge bg-primary ms-2">{{ $categories->firstWhere('id', $categoryId)->name ?? '' }}</span>
                                @endif
                                <i class="bi bi-chevron-{{ $showMobileFilter ? 'up' : 'down' }} ms-auto"></i>
                            </button>
                        </div>

                        <!-- Mobile Filter Content -->
                        <div class="mobile-filter-container">
                            @if($showMobileFilter)
                                <div class="mobile-filter-content">
                                    <div class="category-filter">
                                        <div class="category-select-wrapper">
                                            <select class="form-select form-select-sm bg-dark border-secondary text-light category-select"
                                                    wire:model.live="categoryId">
                                                <option value="">Semua Kategori</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="bi bi-funnel category-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if($search || $categoryId)
        <div class="row mb-4">
            <div class="col-12">
                <div class="active-filters d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted small me-2">Filter aktif:</span>
                    @if($search)
                        <span class="badge bg-primary bg-opacity-25 text-primary border border-primary">
                            Pencarian: {{ $search }}
                            <button wire:click="$set('search', '')" class="btn btn-sm btn-link text-primary p-0 ms-1">
                                <i class="bi bi-x"></i>
                            </button>
                        </span>
                    @endif
                    @if($categoryId)
                        <span class="badge bg-success bg-opacity-25 text-success border border-success">
                            Kategori: {{ $categories->firstWhere('id', $categoryId)->name ?? '' }}
                            <button wire:click="$set('categoryId', '')" class="btn btn-sm btn-link text-success p-0 ms-1">
                                <i class="bi bi-x"></i>
                            </button>
                        </span>
                    @endif
                    <button wire:click="resetAll" class="btn btn-sm btn-outline-secondary ms-auto">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset All
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Results Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="results-info d-flex justify-content-between align-items-center">
                <div class="results-count">
                    <span class="text-light">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        Menampilkan <strong>{{ $products->count() }}</strong> produk
                        @if($products->total() > $products->count())
                            dari <strong>{{ $products->total() }}</strong> total
                        @endif
                    </span>
                </div>
                <div class="view-options d-flex gap-2 align-items-center">
                    <!-- Per Page Selector -->
                    <div class="per-page-selector">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-grid-3x3-gap me-1"></i>
                                {{ $perPage }} / hal
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                                <li>
                                    <button class="dropdown-item text-light {{ $perPage == 4 ? 'active bg-primary' : '' }}"
                                            wire:click="updatePerPage(4)">
                                        <i class="bi bi-grid-1x2 me-2"></i>4 produk / halaman
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-light {{ $perPage == 8 ? 'active bg-primary' : '' }}"
                                            wire:click="updatePerPage(8)">
                                        <i class="bi bi-grid-3x2 me-2"></i>8 produk / halaman
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item text-light {{ $perPage == 12 ? 'active bg-primary' : '' }}"
                                            wire:click="updatePerPage(12)">
                                        <i class="bi bi-grid-3x3 me-2"></i>12 produk / halaman
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="products-grid-container">
        @if($products->count() > 0)
            <x-product-grid :products="$products" columns="col-lg-3 col-md-4 col-sm-6" />
        @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <div class="bg-secondary bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center empty-state-icon" style="width: 120px; height: 120px;">
                            <i class="bi bi-search fs-1 text-muted"></i>
                        </div>
                    </div>
                    <h3 class="text-light mb-3">Produk Tidak Ditemukan</h3>
                    <p class="text-muted mb-4">
                        @if($search || $categoryId)
                            Tidak ada produk yang sesuai dengan filter yang Anda pilih. Coba kata kunci lain atau reset filter.
                        @else
                            Belum ada produk yang tersedia saat ini.
                        @endif
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        @if($search || $categoryId)
                            <button wire:click="resetAll" class="btn btn-primary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset Filter
                            </button>
                        @endif
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i>Kembali ke Beranda
                        </a>
                    </div>
                      @endif
    </div>

    <!-- Pagination -->
    @if($products->count() > 0 && $products->hasPages())
        <div class="row mt-5">
            <div class="col-12">
                <div class="pagination-wrapper">
                    {{ $products->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    @endif
</div>
