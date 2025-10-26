<x-app-layout 
    :meta-title="$product->name . ' - ' . config('app.name')"
    :meta-description="Str::limit(strip_tags($product->description ?? ''), 160) ?: 'Beli ' . $product->name . ' dengan harga terbaik. Stok terbatas!'"
    :og-type="'product'"
    :og-title="$product->name"
    :og-description="Str::limit(strip_tags($product->description ?? ''), 160) ?: 'Beli ' . $product->name . ' dengan harga terbaik. Stok terbatas!'"
    :og-image="$product->images ? asset('storage/' . $product->images[0]) : asset('images/placeholder.png')"
    :og-url="url('/produk/' . $product->slug)"
    :twitter-title="$product->name"
    :twitter-description="Str::limit(strip_tags($product->description ?? ''), 160) ?: 'Beli ' . $product->name . ' dengan harga terbaik. Stok terbatas!'"
    :twitter-image="$product->images ? asset('storage/' . $product->images[0]) : asset('images/placeholder.png')"
>
    @livewire('product-detail', ['slug' => $slug])
</x-app-layout>