<div class="cart-page">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10 col-xxl-8">

            <!-- Header Section -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 pb-3 border-bottom border-secondary">
                <div class="mb-3 mb-md-0">
                    <h1 class="h3 fw-bold text-light mb-1">Keranjang Belanja</h1>
                    <p class="text-muted small mb-0">{{ count($cart) }} produk dalam keranjang</p>
                </div>
                <a href="{{ route('catalog') }}" class="btn btn-outline-primary btn-sm px-3">
                    <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
                </a>
            </div>

            <!-- Alert Messages -->
            @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(empty($cart))
            <!-- Empty Cart State -->
            <div class="text-center py-5 my-5">
                <div class="mb-4">
                    <div class="bg-secondary bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                        <i class="bi bi-cart-x display-4 text-muted"></i>
                    </div>
                </div>
                <h3 class="text-light mb-3 fw-bold">Keranjang Belanja Kosong</h3>
                <p class="text-muted mb-4 lead">Belum ada produk dalam keranjang Anda</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg px-4 py-3 shadow-sm">
                    <i class="bi bi-shop me-2"></i>Mulai Belanja Sekarang
                </a>
            </div>
            @else
            <div class="row g-4">

                <!-- Cart Items Section -->
                <div class="col-lg-8">
                    <div class="card border-secondary shadow-sm">
                        <div class="card-header bg-dark border-secondary py-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-receipt me-2 text-primary"></i>
                                <h5 class="mb-0 text-light fw-semibold">Detail Produk</h5>
                            </div>
                        </div>
                        <div class="card-body p-0">

                            <!-- Desktop Header (Hidden on Mobile) -->
                            <div class="d-none d-md-block bg-light bg-opacity-5 py-3 px-4 border-bottom border-secondary">
                                <div class="row align-items-center fw-semibold text-muted small">
                                    <div class="col-md-6">Produk</div>
                                    <div class="col-md-2 text-center">Kuantitas</div>
                                    <div class="col-md-2 text-center">Harga</div>
                                    <div class="col-md-2 text-center">Total</div>
                                </div>
                            </div>

                            <!-- Cart Items -->
                            @foreach($cart as $productId => $item)
                            <div class="cart-item border-bottom border-secondary {{ !$loop->last ? 'border-opacity-25' : '' }}">
                                <div class="p-3 p-md-4">
                                    <div class="row align-items-center g-3">

                                        <!-- Product Image & Info -->
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    @if($item['image'])
                                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                                         class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                    <div class="bg-secondary bg-opacity-25 rounded d-flex align-items-center justify-content-center"
                                                         style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <h6 class="mb-1 text-light fw-semibold text-truncate">{{ $item['name'] }}</h6>
                                                    <p class="mb-0 text-primary fw-medium small">
                                                        Rp {{ number_format($item['price'], 0, ',', '.') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="col-6 col-md-2">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="input-group input-group-sm" style="width: fit-content;">
                                                    <button class="btn btn-outline-secondary btn-sm px-2"
                                                            wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] - 1 }})"
                                                            {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="text" class="form-control form-control-sm bg-dark border-secondary text-light text-center fw-semibold"
                                                           style="width: 50px;" value="{{ $item['quantity'] }}" readonly>
                                                    <button class="btn btn-outline-secondary btn-sm px-2"
                                                            wire:click="updateQuantity({{ $productId }}, {{ $item['quantity'] + 1 }})">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Unit Price (Hidden on Mobile) -->
                                        <div class="col-3 col-md-2 d-none d-md-block">
                                            <div class="text-center">
                                                <span class="text-muted small">Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <!-- Total Price -->
                                        <div class="col-3 col-md-2">
                                            <div class="d-flex align-items-center justify-content-between justify-content-md-center">
                                                <div class="text-end text-md-center">
                                                    <strong class="text-primary fw-semibold">
                                                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                                    </strong>
                                                </div>
                                                <!-- Remove Button (Mobile) -->
                                                <button class="btn btn-sm btn-outline-danger ms-2 d-md-none"
                                                        wire:click="removeFromCart({{ $productId }})"
                                                        wire:confirm="Hapus produk ini dari keranjang?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Remove Button (Desktop) -->
                                        <div class="col-12 col-md-auto d-none d-md-block">
                                            <button class="btn btn-sm btn-outline-danger"
                                                    wire:click="removeFromCart({{ $productId }})"
                                                    wire:confirm="Hapus produk ini dari keranjang?"
                                                    title="Hapus produk">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <!-- Order Summary Section -->
                <div class="col-lg-4">
                    <div class="card border-secondary shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-dark border-secondary py-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calculator me-2 text-primary"></i>
                                <h5 class="mb-0 text-light fw-semibold">Ringkasan Pesanan</h5>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- Order Details -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Jumlah Produk</span>
                                    <span class="text-light fw-medium">{{ count($cart) }} item</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Item</span>
                                    <span class="text-light fw-medium">{{ array_sum(array_column($cart, 'quantity')) }} pcs</span>
                                </div>
                            </div>

                            <hr class="border-secondary my-3">

                            <!-- Total Amount -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-light fw-semibold h6 mb-0">Total Pembayaran</span>
                                    <span class="text-primary fw-bold h4 mb-0">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <button class="btn btn-success btn-lg w-100 mb-3 shadow-sm fw-semibold"
                                    wire:click="checkout"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="bi bi-whatsapp me-2"></i>Checkout via WhatsApp
                                </span>
                                <span wire:loading>
                                    <i class="bi bi-hourglass-split me-2"></i>Memproses...
                                </span>
                            </button>

                            <!-- Info Text -->
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Pesanan akan dikonfirmasi melalui WhatsApp
                                </small>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>
</div>