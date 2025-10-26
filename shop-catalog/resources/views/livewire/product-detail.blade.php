<div class="container my-5">
    <!-- Breadcrumbs -->
    <x-breadcrumbs :items="[
        ['label' => 'Katalog', 'url' => route('catalog')],
        ['label' => $product->category->name, 'url' => route('catalog', ['category' => $product->category_id])],
        ['label' => $product->name, 'url' => '']
    ]" />
    
    <!-- Product Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs nav-tabs-modern border-0" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab" aria-controls="detail" aria-selected="true">
                        <i class="bi bi-info-circle me-2"></i>Detail Produk
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                        <i class="bi bi-star me-2"></i>Ulasan
                        @if($product->total_reviews > 0)
                            <span class="badge bg-primary ms-1">{{ $product->total_reviews }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="productTabsContent">
        <!-- Detail Tab -->
        <div class="tab-pane fade show active" id="detail" role="tabpanel" aria-labelledby="detail-tab" tabindex="0">
            <div class="row">
                <div class="col-md-6">
            @if($product->has_variants && $selectedVariant && $selectedVariant->hasCustomImages())
                <!-- Variant Images -->
                <div id="variantCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($selectedVariant->all_image_urls as $index => $imageUrl)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ $imageUrl }}" class="d-block w-100" alt="{{ $selectedVariant->name }}">
                        </div>
                        @endforeach
                    </div>
                    @if(count($selectedVariant->all_image_urls) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#variantCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#variantCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    @endif
                </div>
            @elseif($product->images)
                <!-- Default Product Images -->
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($product->images as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="{{ $product->name }}">
                        </div>
                        @endforeach
                    </div>
                    @if(count($product->images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    @endif
                </div>
            @else
                <!-- Placeholder Image -->
                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                    <span class="text-muted">No images available</span>
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            <p class="text-muted">Kategori: {{ $product->category->name }}</p>
            @if($product->has_variants)
                @if($selectedVariant)
                    <h3 class="text-success">Rp {{ number_format($this->current_price, 0, ',', '.') }}</h3>
                    <p class="text-muted small">{{ $selectedVariant->name }}</p>
                @else
                    <h3 class="text-success">{{ $this->priceRange }}</h3>
                    <p class="text-muted small">Harga tergantung variant yang dipilih</p>
                @endif
            @else
                <h3 class="text-success">Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
                @if($product->discount_price)
                <p class="text-muted"><s>Rp {{ number_format($product->discount_price, 0, ',', '.') }}</s></p>
                @endif
            @endif
            @if($product->total_reviews > 0)
                <div class="product-rating-detail mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rating-display">
                            {!! $product->starsAttribute !!}
                        </div>
                        <div class="rating-info">
                            <span class="text-warning fw-bold">{{ $product->formatted_average_rating }}</span>
                            <span class="text-muted">({{ $product->total_reviews }} ulasan)</span>
                        </div>
                    </div>
                </div>
            @endif
            <div class="product-description-container">
        <div class="product-description {{ $showFullDescription ? 'expanded' : 'collapsed' }}">
            {!! $this->displayDescription !!}
        </div>

        @if($this->needsTruncation)
            <button wire:click="toggleDescription"
                    class="btn btn-link text-primary p-0 mt-2 description-toggle-btn">
                <span class="toggle-text">
                    {{ $showFullDescription ? 'Lihat Lebih Sedikit' : 'Lihat Selengkapnya' }}
                </span>
                <i class="bi bi-chevron-{{ $showFullDescription ? 'up' : 'down' }} ms-1"></i>
            </button>
        @endif
    </div>

            <!-- Variant Selection -->
            @if($product->has_variants)
            <div class="variant-selection mb-4">
                <h5 class="text-light mb-3">Pilih Variant</h5>

                <!-- Size Selection -->
                @if(count($this->availableSizes) > 0)
                <div class="mb-3">
                    <label class="form-label text-light">Ukuran</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($this->availableSizes as $size)
                            <button type="button"
                                    class="btn variant-option {{ $selectedSize === $size ? 'btn-primary' : 'btn-outline-secondary' }}"
                                    wire:click="$set('selectedSize', '{{ $size }}')"
                                    wire:loading.attr="disabled">
                                {{ $size }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Color Selection -->
                @if(count($this->availableColors) > 0)
                <div class="mb-3">
                    <label class="form-label text-light">Warna</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($this->availableColors as $color)
                            <button type="button"
                                    class="btn variant-option {{ $selectedColor === $color ? 'btn-primary' : 'btn-outline-secondary' }}"
                                    wire:click="$set('selectedColor', '{{ $color }}')"
                                    wire:loading.attr="disabled">
                                {{ $color }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Selected Variant Info -->
                @if($selectedVariant)
                <div class="selected-variant-info bg-secondary bg-opacity-25 rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-light mb-1">{{ $selectedVariant->name }}</h6>
                            <p class="text-muted small mb-0">SKU: {{ $selectedVariant->sku }}</p>
                        </div>
                        <div class="text-end">
                            @if($selectedVariant->has_discount)
                                <small class="text-muted text-decoration-line-through">{{ $selectedVariant->formatted_price }}</small>
                            @endif
                            <div class="text-primary fw-bold">{{ $selectedVariant->formatted_final_price }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Variant Selection Error -->
                @if(session('error'))
                <div class="alert alert-danger alert-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
                @endif
            </div>
            @endif

            <div class="mb-4">
                <label class="form-label text-light">Jumlah</label>
                <div class="d-flex align-items-center product-detail-quantity">
                    <div class="input-group input-group-sm" style="width: 120px;">
                        <button class="btn btn-outline-secondary"
                                wire:click="decrementQuantity"
                                @disabled($quantity <= 1)>
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number"
                               class="form-control text-center bg-dark border-secondary text-light"
                               wire:model.live="quantity"
                               min="1"
                               max="{{ $this->current_stock }}"
                               value="{{ $quantity }}">
                        <button class="btn btn-outline-secondary"
                                wire:click="incrementQuantity"
                                @disabled($quantity >= $this->current_stock)>
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <span class="text-muted small ms-3">
                        Stok: {{ $this->current_stock == 999 ? 'Tersedia' : $this->current_stock }} pcs
                    </span>
                </div>
            </div>
            <div class="product-action-buttons">
                <button class="btn btn-primary flex-fill" wire:click="addToCart">
                    <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
                </button>
                <button class="btn btn-whatsapp flex-fill" wire:click="proceedToCheckout">
                    <i class="bi bi-whatsapp me-2"></i>Pesan Sekarang
                </button>
                <div class="dropdown flex-fill">
                    <button class="btn btn-share dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-share me-2"></i>Share
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary">
                        <li>
                            <button class="dropdown-item text-light copy-link-btn" data-url="{{ url('/produk/' . $product->slug) }}">
                                <i class="bi bi-link-45deg me-2"></i>Copy Link
                            </button>
                        </li>
                        <li>
                            <a class="dropdown-item text-light" href="https://wa.me/?text={{ urlencode('Cek produk keren ini: ' . $product->name . ' - ' . url('/produk/' . $product->slug)) }}" target="_blank">
                                <i class="bi bi-whatsapp me-2"></i>Share ke WhatsApp
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-light" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/produk/' . $product->slug)) }}" target="_blank">
                                <i class="bi bi-facebook me-2"></i>Share ke Facebook
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-light" href="https://twitter.com/intent/tweet?text={{ urlencode('Cek produk keren ini: ' . $product->name) }}&url={{ urlencode(url('/produk/' . $product->slug)) }}" target="_blank">
                                <i class="bi bi-twitter me-2"></i>Share ke Twitter
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="modern-toast success-toast" wire:ignore>
        <div class="toast-content">
            <i class="bi bi-check-circle-fill toast-icon"></i>
            <div class="toast-message">
                <h4>Sukses!</h4>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Checkout Form Modal -->
    @if($showForm)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" style="z-index: 1050;" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content bg-dark border-secondary">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-light">
                            <i class="bi bi-person-lines-fill text-primary me-2"></i>Data Pembeli
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cancelCheckout"></button>
                    </div>
                    <form wire:submit.prevent="checkoutNow" class="checkout-form">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="customerName" class="form-label text-light">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control bg-dark border-secondary text-light @error('customerName') is-invalid @enderror"
                                       id="customerName"
                                       wire:model="customerName"
                                       placeholder="Masukkan nama lengkap Anda">
                                @error('customerName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="customerAddress" class="form-label text-light">
                                    Alamat Lengkap <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control bg-dark border-secondary text-light @error('customerAddress') is-invalid @enderror"
                                          id="customerAddress"
                                          wire:model="customerAddress"
                                          rows="3"
                                          placeholder="Masukkan alamat pengiriman lengkap"></textarea>
                                @error('customerAddress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Order Summary in Modal -->
                            <div class="bg-secondary bg-opacity-25 rounded-3 p-3 mb-3 order-summary-box">
                                <h6 class="text-light mb-2">Ringkasan Pesanan:</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Produk:</span>
                                    <span class="text-light small">
                                        @if($product->has_variants && $selectedVariant)
                                            {{ $selectedVariant->name }}
                                        @else
                                            {{ $product->name }}
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Quantity:</span>
                                    <span class="text-light small">{{ $quantity }} pcs</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Total Bayar:</span>
                                    <span class="text-primary fw-bold small">Rp {{ number_format($this->current_price * $quantity, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" wire:click="cancelCheckout">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-whatsapp">
                                <i class="bi bi-whatsapp me-1"></i>Kirim ke WhatsApp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab" tabindex="0">
            <livewire:product-reviews :slug="$product->slug" />
        </div>
    </div>
</div>
