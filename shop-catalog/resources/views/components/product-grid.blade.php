<div class="row">
    @foreach($products as $product)
    <div class="{{ $columns }} mb-3">
        <x-product-card :product="$product" />
    </div>
    @endforeach
</div>