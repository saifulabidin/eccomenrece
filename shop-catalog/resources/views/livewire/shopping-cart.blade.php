<div>
    <!-- Cart Button -->
    <a href="{{ route('cart') }}" class="btn btn-outline-light position-relative rounded-pill px-3 py-2 ms-3">
        <i class="bi bi-cart-fill me-2"></i>
        <span class="d-none d-lg-inline">Keranjang</span>
        @if(count($cart) > 0)
        <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">{{ count($cart) }}</span>
        @endif
    </a>
</div>