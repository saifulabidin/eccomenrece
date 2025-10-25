<div class="product-card card">
    <x-product-image :product="$product" />
    <div class="card-body d-flex flex-column">
        <x-product-info :product="$product" />
        <div class="mt-auto">
            <x-product-actions :product="$product" />
        </div>
    </div>
</div>