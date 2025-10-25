<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StoreConfig;
use Livewire\Component;

class CartPage extends Component
{
    public $cart = [];
    public $total = 0;
    public $itemToRemove = null;

    protected $listeners = ['add-to-cart' => 'addToCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session('cart', []);
        $this->calculateTotal();
    }

    public function addToCart($data)
    {
        $productId = $data['productId'];
        $quantity = $data['quantity'] ?? 1;

        $product = Product::find($productId);
        if (!$product) return;

        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->images[0] ?? null,
            ];
        }

        session(['cart' => $cart]);
        $this->loadCart();

        $this->dispatch('cart-updated');
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function updateQuantity($productId, $quantity)
    {
        $cart = session('cart', []);

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        session(['cart' => $cart]);
        $this->loadCart();
    }

    public function removeFromCart()
    {
        if (!$this->itemToRemove) {
            session()->flash('error', 'Tidak ada produk yang dipilih untuk dihapus!');
            return;
        }

        $cart = session('cart', []);
        $productName = $cart[$this->itemToRemove]['name'] ?? 'Produk';
        unset($cart[$this->itemToRemove]);
        session(['cart' => $cart]);
        $this->loadCart();

        // Reset the item to remove
        $this->itemToRemove = null;

        session()->flash('success', "{$productName} berhasil dihapus dari keranjang!");
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['quantity'];
        }
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        $storeConfig = StoreConfig::first();
        $whatsappNumber = $storeConfig->whatsapp_number ?? '6282242034791';

        $message = "Halo! Saya ingin memesan produk berikut:\n\n";

        foreach ($this->cart as $item) {
            $message .= "• {$item['name']} (Qty: {$item['quantity']}) - Rp " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "\n";
        }

        $message .= "\nTotal: Rp " . number_format($this->total, 0, ',', '.') . "\n\n";
        $message .= "Mohon konfirmasi pesanan saya. Terima kasih!";

        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        // Clear cart after checkout
        session(['cart' => []]);
        $this->cart = [];
        $this->total = 0;

        return redirect($whatsappUrl);
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}