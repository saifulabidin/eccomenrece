<div class="card h-100 shadow-sm border-0">
    <x-product-image :product="$product" />
    <div class="card-body d-flex flex-column">
        <x-product-info :product="$product" />
        <div class="mt-auto">
            <x-product-actions :product="$product" />
        </div>
    </div>
</div>