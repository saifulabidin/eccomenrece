<div class="card h-100 text-center border-0 shadow-sm">
    <div class="card-body d-flex flex-column justify-content-center py-4">
        <div class="category-icon mb-3">
            <i class="bi bi-grid-3x3-gap display-4 text-primary"></i>
        </div>
        <h5 class="card-title fw-bold mb-3">{{ $category->name }}</h5>
        <p class="text-muted small mb-3">{{ $category->description ?? 'Koleksi produk berkualitas' }}</p>
        <a href="{{ route('catalog', ['categoryId' => $category->id]) }}" class="btn btn-outline-primary mt-auto">
            Lihat Produk <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>