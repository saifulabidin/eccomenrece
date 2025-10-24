@if($product->images)
<img src="{{ asset('storage/' . $product->images[0]) }}" class="card-img-top" alt="{{ $product->name }}">
@endif