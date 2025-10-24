<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1>Katalog Produk</h1>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Cari produk..." wire:model.live="search">
        </div>
        <div class="col-md-6">
            <select class="form-select" wire:model.live="categoryId">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <x-product-grid :products="$products" columns="col-md-3" />

    <div class="row">
        <div class="col-12">
            {{ $products->links() }}
        </div>
    </div>
</div>
