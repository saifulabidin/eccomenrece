<div>
    <!-- Cart Button -->
    <button class="btn btn-outline-primary position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
        <i class="bi bi-cart"></i> Keranjang
        <span class="badge bg-secondary">{{ count($cart) }}</span>
    </button>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Keranjang Belanja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(empty($cart))
                    <p>Keranjang kosong.</p>
                    <script>
                        setTimeout(() => {
                            const modal = document.getElementById('cartModal');
                            if (modal && modal.classList.contains('show')) {
                                const bsModal = bootstrap.Modal.getInstance(modal);
                                if (bsModal) bsModal.hide();
                            }
                        }, 100);
                    </script>
                    @else
                    <div class="list-group">
                        @foreach($cart as $productId => $item)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $item['name'] }}</h6>
                                    <p class="mb-1">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control me-2" style="width: 80px;" wire:change="updateQuantity({{ $productId }}, $event.target.value)" value="{{ $item['quantity'] }}" min="1">
                                <button class="btn btn-sm btn-danger" wire:click="removeFromCart({{ $productId }})" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Hapus</span>
                                    <span wire:loading>Menghapus...</span>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    @if(!empty($cart))
                    <button class="btn btn-success" wire:click="checkout">Checkout via WhatsApp</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>