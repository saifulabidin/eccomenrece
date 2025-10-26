<h6 class="card-title fw-bold mb-2">{{ Str::limit($product->name, 50) }}</h6>
<p class="card-text text-primary fw-bold mb-2">{{ $product->display_price }}</p>
@if($product->has_variants)
    <div class="variant-indicator mb-2">
        <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 me-1">
            <i class="bi bi-collection me-1"></i>Multiple Variants
        </span>
    </div>
@endif
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