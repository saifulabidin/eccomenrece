<div>
    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(empty($cart))
            <!-- Empty Cart -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="bg-secondary bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center empty-cart-icon" style="width: 100px; height: 100px;">
                        <i class="bi bi-cart-x fs-1 text-muted"></i>
                    </div>
                </div>
                <h3 class="text-light mb-2">Keranjang Belanja Kosong</h3>
                <p class="text-muted mb-3">Belum ada produk dalam keranjang Anda</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary px-4">
                    <i class="bi bi-shop me-2"></i>Mulai Belanja
                </a>
            </div>
        @else
            <div class="row g-4">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card bg-dark border-secondary">
                        <div class="card-header border-secondary">
                            <h5 class="mb-0 text-light">
                                <i class="bi bi-list-ul me-2"></i>Daftar Produk
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            @foreach($cart as $productId => $item)
                                <?php $product = \App\Models\Product::find($productId); ?>
                                <div class="cart-item border-bottom border-secondary p-3">
                                    <div class="row g-3 align-items-center">
                                        <!-- Image -->
                                        <div class="col-3 col-md-2">
                                            <div class="bg-secondary rounded overflow-hidden" style="aspect-ratio: 1;">
                                                @if($item['image'])
                                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                                         class="w-100 h-100 object-fit-cover"
                                                         alt="{{ $item['name'] }}">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="col-9 col-md-5">
                                            <h6 class="text-light mb-1">{{ $item['name'] }}</h6>
                                            <p class="text-muted small mb-0">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                                            <div class="text-primary fw-semibold mt-1">
                                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </div>
                                        </div>

                                        <!-- Quantity -->
                                        <div class="col-6 col-md-3">
                                            <div class="d-flex align-items-center">
                                                <label class="text-muted small me-2">Qty:</label>
                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary" wire:click="decrementQuantity({{ $productId }})" @disabled($item['quantity'] <= 1)>
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number"
                                                           class="form-control text-center bg-dark border-secondary text-light"
                                                           wire:model.live="cart.{{ $productId }}.quantity"
                                                           min="1"
                                                           value="{{ $item['quantity'] }}">
                                                    <button class="btn btn-outline-secondary" wire:click="incrementQuantity({{ $productId }})">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="col-6 col-md-2 text-end">
                                            <div class="text-light fw-bold mb-2">
                                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger"
                                                    wire:click="confirmRemove({{ $productId }})"
                                                    title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer border-secondary">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="btn btn-danger" wire:click="clearCart">
                                    <i class="bi bi-trash3 me-1"></i>Kosongkan Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card bg-dark border-secondary cart-summary">
                        <div class="card-header border-secondary">
                            <h5 class="mb-0 text-light">
                                <i class="bi bi-calculator me-2"></i>Ringkasan Pesanan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Item</span>
                                    <span class="text-light">{{ array_sum(array_column($cart, 'quantity')) }} pcs</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Jenis Produk</span>
                                    <span class="text-light">{{ count($cart) }} item</span>
                                </div>
                            </div>
                            <hr class="border-secondary">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="text-light fw-bold">Total Pembayaran</span>
                                <span class="text-primary h4 mb-0 fw-bold">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>
                            <button class="btn btn-whatsapp w-100 py-2" wire:click="proceedToCheckout">
                                <i class="bi bi-whatsapp me-2"></i>Checkout via WhatsApp
                            </button>
                            <p class="text-muted small text-center mt-2 mb-0">
                                <i class="bi bi-shield-check me-1"></i>Pembayaran aman via WhatsApp
                            </p>
                        </div>
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
                        <form wire:submit.prevent="checkout" class="checkout-form">
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
                                        <span class="text-muted small">Total Item:</span>
                                        <span class="text-light small">{{ array_sum(array_column($cart, 'quantity')) }} pcs</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Total Bayar:</span>
                                        <span class="text-primary fw-bold small">Rp {{ number_format($total, 0, ',', '.') }}</span>
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

    <!-- Delete Confirmation Modal -->
    @if($itemToRemove)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" style="z-index: 1050;" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark border-secondary">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-light">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>Konfirmasi Hapus
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="$set('itemToRemove', null)"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-light mb-0">Apakah Anda yakin ingin menghapus produk ini dari keranjang?</p>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" wire:click="$set('itemToRemove', null)">Batal</button>
                        <button type="button" class="btn btn-danger" wire:click="removeFromCart">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>