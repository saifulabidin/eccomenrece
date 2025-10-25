<h6 class="card-title fw-bold mb-2">{{ Str::limit($product->name, 50) }}</h6>
<p class="card-text text-primary fw-bold mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
@if($product->total_reviews > 0)
    <div class="product-rating mb-2">
        <span class="rating-stars small">
            {!! $product->starsAttribute !!}
        </span>
        <span class="rating-info text-muted small ms-1">
            {{ $product->formatted_average_rating }} ({{ $product->total_reviews }} ulasan)
        </span>
    </div>
@endif