<div>
    <!-- Desktop Cart Button -->
    <a href="{{ route('cart') }}" class="cart-btn cart-btn-desktop" title="Keranjang Belanja">
        <i class="fas fa-shopping-cart cart-icon"></i>
        <span class="cart-text">Keranjang</span>
        @if(count($cart) > 0)
        <span class="cart-badge">{{ count($cart) }}</span>
        @endif
    </a>

    <!-- Mobile Cart Button -->
    <a href="{{ route('cart') }}" class="cart-btn cart-btn-mobile" title="Keranjang Belanja">
        <i class="fas fa-shopping-cart cart-icon-mobile"></i>
        @if(count($cart) > 0)
        <span class="cart-badge-mobile">{{ count($cart) }}</span>
        @endif
    </a>
</div>