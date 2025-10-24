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

    public function render()
    {
        return view('livewire.product-detail');
    }
}
