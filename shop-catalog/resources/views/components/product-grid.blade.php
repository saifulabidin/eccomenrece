<div class="row g-4">
    @foreach($products as $product)
    <div class="{{ $columns }} product-grid-item">
        <x-product-card :product="$product" />
    </div>
    @endforeach
</div>