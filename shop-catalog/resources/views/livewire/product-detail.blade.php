<div class="container my-5">
    <div class="row">
        <div class="col-md-6">
            @if($product->images)
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
            @endif
        </div>
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            <p class="text-muted">Kategori: {{ $product->category->name }}</p>
            <h3 class="text-success">Rp {{ number_format($product->price, 0, ',', '.') }}</h3>
            @if($product->discount_price)
            <p class="text-muted"><s>Rp {{ number_format($product->discount_price, 0, ',', '.') }}</s></p>
            @endif
            <p>{{ $product->description }}</p>
            <div class="mb-3">
                <label for="quantity" class="form-label">Jumlah</label>
                <input type="number" class="form-control" id="quantity" wire:model="quantity" min="1" max="{{ $product->stock ?? 999 }}">
            </div>
            <button class="btn btn-primary" wire:click="addToCart">Tambah ke Keranjang</button>
            <button class="btn btn-success ms-2" wire:click="checkoutNow">Pesan Sekarang</button>
        </div>
    </div>

    @if(session('success'))
    <div class="modern-toast success-toast" wire:ignore>
        <div class="toast-content">
            <i class="fas fa-check-circle toast-icon"></i>
            <div class="toast-message">
                <h4>Sukses!</h4>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
