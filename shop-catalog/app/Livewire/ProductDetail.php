<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StoreConfig;
use Livewire\Component;

class ProductDetail extends Component
{
    public $product;
    public $quantity = 1;
    public $customerName = '';
    public $customerAddress = '';
    public $showForm = false;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->where('status', 'published')->firstOrFail();
    }

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

    public function proceedToCheckout()
    {
        $this->showForm = true;
    }

    public function cancelCheckout()
    {
        $this->showForm = false;
        $this->reset(['customerName', 'customerAddress']);
    }

    public function incrementQuantity()
    {
        $maxStock = $this->product->stock ?? 999;
        if ($this->quantity < $maxStock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function checkoutNow()
    {
        $this->validate();

        $message = "PESANAN BARU\n\n";
        $message .= "Data Pelanggan:\n";
        $message .= "Nama: {$this->customerName}\n";
        $message .= "Alamat: {$this->customerAddress}\n\n";
        $message .= "Detail Pesanan:\n";
        $message .= "• {$this->product->name} (Qty: {$this->quantity}) - Rp " . number_format($this->product->price * $this->quantity, 0, ',', '.') . "\n";
        $message .= "\nTotal Pembayaran: Rp " . number_format($this->product->price * $this->quantity, 0, ',', '.') . "\n\n";
        $message .= "Mohon konfirmasi pesanan saya. Terima kasih!";

        $config = StoreConfig::first() ?? (object)['whatsapp_number' => '6281234567890'];
        $whatsappNumber = $config->whatsapp_number;

        $this->showForm = false;
        $this->reset(['customerName', 'customerAddress']);

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
