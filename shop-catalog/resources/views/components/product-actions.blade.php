<a href="{{ route('product.detail', $product->slug) }}" class="btn btn-outline-primary btn-sm me-2 mb-2">
    <i class="bi bi-eye me-1"></i>Lihat Detail
</a>
@if(!$product->has_variants)
    <button class="btn btn-primary btn-sm mb-2" wire:click="$dispatch('add-to-cart', { productId: {{ $product->id }}, quantity: 1 })">
        <i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang
    </button>
@endif