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
    public $customerName = '';
    public $customerAddress = '';
    public $showForm = false;

    public function confirmRemove($productId)
    {
        $this->itemToRemove = $productId;
    }

    protected $listeners = ['add-to-cart' => 'addToCart'];

    protected $rules = [
        'customerName' => 'required|string|min:3|max:50',
        'customerAddress' => 'required|string|min:10|max:200',
    ];

    protected $messages = [
        'customerName.required' => 'Nama wajib diisi',
        'customerName.min' => 'Nama minimal 3 karakter',
        'customerName.max' => 'Nama maksimal 50 karakter',
        'customerAddress.required' => 'Alamat wajib diisi',
        'customerAddress.min' => 'Alamat minimal 10 karakter',
        'customerAddress.max' => 'Alamat maksimal 200 karakter',
    ];

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

    public function incrementQuantity($productId)
    {
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
            session(['cart' => $cart]);
            $this->loadCart();
        }
    }

    public function decrementQuantity($productId)
    {
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            if ($cart[$productId]['quantity'] > 1) {
                $cart[$productId]['quantity']--;
            } else {
                unset($cart[$productId]);
            }
            session(['cart' => $cart]);
            $this->loadCart();
        }
    }

    public function clearCart()
    {
        session(['cart' => []]);
        $this->loadCart();
        session()->flash('success', 'Keranjang berhasil dikosongkan!');
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

    public function proceedToCheckout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        $this->showForm = true;
    }

    public function cancelCheckout()
    {
        $this->showForm = false;
        $this->reset(['customerName', 'customerAddress']);
    }

    public function checkout()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        $storeConfig = StoreConfig::first();
        $whatsappNumber = $storeConfig->whatsapp_number ?? '6282242034791';

        $message = "PESANAN BARU\n\n";
        $message .= "Data Pelanggan:\n";
        $message .= "Nama: {$this->customerName}\n";
                $message .= "Alamat: {$this->customerAddress}\n\n";
        $message .= "Detail Pesanan:\n";

        foreach ($this->cart as $item) {
            $message .= "• {$item['name']} (Qty: {$item['quantity']}) - Rp " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "\n";
        }

        $message .= "\nTotal Pembayaran: Rp " . number_format($this->total, 0, ',', '.') . "\n\n";
        $message .= "Mohon konfirmasi pesanan saya. Terima kasih!";

        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        // Clear cart after checkout
        session(['cart' => []]);
        $this->cart = [];
        $this->total = 0;
        $this->showForm = false;
        $this->reset(['customerName', 'customerAddress']);

        return redirect($whatsappUrl);
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}