<h6 class="card-title fw-bold mb-2">{{ Str::limit($product->name, 50) }}</h6>
<p class="card-text text-primary fw-bold mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>