<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StoreConfig;
use Livewire\Component;

class ShoppingCart extends Component
{
    public $cart = [];
    public $showModal = false;
    public $total = 0;

    protected $listeners = ['add-to-cart' => 'addToCart'];

    public function mount()
    {
        $this->cart = session('cart', []);
        $this->calculateTotal();
    }

    public function addToCart($productId, $quantity = 1)
    {
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
                'image' => $product->images ? $product->images[0] : null,
            ];
        }

        session(['cart' => $cart]);
        $this->cart = $cart;
        $this->calculateTotal();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $cart = session('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session(['cart' => $cart]);
            $this->cart = $cart;
            $this->calculateTotal();
        }
    }

    public function removeFromCart($productId)
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);
        $this->cart = $cart;
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $this->total = array_reduce($this->cart, function ($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function checkout()
    {
        // Redirect to WhatsApp
        $message = $this->generateWhatsAppMessage();
        $config = $this->getStoreConfig();
        $whatsappNumber = $config->whatsapp_number;
        return redirect()->away("https://api.whatsapp.com/send?phone={$whatsappNumber}&text=" . urlencode($message));
    }

    private function generateWhatsAppMessage()
    {
        $message = "Halo, saya ingin memesan:\n\n";
        foreach ($this->cart as $item) {
            $message .= "{$item['name']} (x{$item['quantity']}) - Rp " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "\n";
        }
        $message .= "\nTotal: Rp " . number_format($this->total, 0, ',', '.') . "\n";
        return $message;
    }

    private function getStoreConfig()
    {
        return StoreConfig::first() ?? (object)['whatsapp_number' => '6281234567890'];
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
