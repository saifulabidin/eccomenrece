<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StoreConfig;
use Livewire\Component;

class ProductDetail extends Component
{
    public $product;
    public $quantity = 1;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->where('status', 'published')->firstOrFail();
    }

    public function checkoutNow()
    {
        $message = "Halo, saya ingin memesan:\n\n";
        $message .= "{$this->product->name} (x{$this->quantity}) - Rp " . number_format($this->product->price * $this->quantity, 0, ',', '.') . "\n";
        $message .= "\nTotal: Rp " . number_format($this->product->price * $this->quantity, 0, ',', '.') . "\n";

        $config = StoreConfig::first() ?? (object)['whatsapp_number' => '6281234567890'];
        $whatsappNumber = $config->whatsapp_number;
        return redirect()->away("https://api.whatsapp.com/send?phone={$whatsappNumber}&text=" . urlencode($message));
    }

    public function addToCart()
    {
        $cart = session('cart', []);

        if (isset($cart[$this->product->id])) {
            $cart[$this->product->id]['quantity'] += $this->quantity;
        } else {
            $cart[$this->product->id] = [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->price,
                'quantity' => $this->quantity,
                'image' => $this->product->images[0] ?? null,
            ];
        }

        session(['cart' => $cart]);

        // Dispatch event to update cart count in navbar
        $this->dispatch('cart-updated');

        // Flash success message - stay on the same page
        session()->flash('success', 'Produk berhasil ditambahkan ke keranjang!');

        // Reset quantity after adding to cart
        $this->quantity = 1;
    }

    public function render()
    {
        return view('livewire.product-detail');
    }
}
