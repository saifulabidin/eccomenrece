<div class="card h-100 text-center border-0 shadow-sm">
    <div class="card-body d-flex flex-column justify-content-center py-4">
        <h5 class="card-title fw-bold mb-3 text-light">{{ $category->name }}</h5>
        <p class="text-muted small mb-3">{{ $category->description ?? 'Koleksi produk berkualitas' }}</p>
        @if($category->image_url)
            <div class="category-image mb-4">
                <img src="{{ $category->image_url }}"
                     class="rounded-3 border-0"
                     style="width: 140px; height: 140px; object-fit: cover; box-shadow: 0 8px 25px rgba(0,0,0,0.15);"
                     alt="{{ $category->name }}">
            </div>
        @else
            <div class="category-icon mb-4">
                <i class="bi bi-grid-3x3-gap" style="font-size: 4rem;"></i>
            </div>
        @endif
        <a href="{{ route('catalog', ['categoryId' => $category->id]) }}" class="btn btn-outline-primary mt-auto">
            Lihat Produk <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>