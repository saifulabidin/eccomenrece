<?php

namespace App\Livewire;

use Livewire\Component;

class CartBadge extends Component
{
    public $cartCount = 0;

    protected $listeners = ['cart-updated' => 'updateCartCount'];

    public function mount()
    {
        $this->updateCartCount();
    }

    public function updateCartCount()
    {
        $cart = session('cart', []);
        $this->cartCount = array_reduce($cart, function ($total, $item) {
            return $total + $item['quantity'];
        }, 0);
    }

    public function render()
    {
        return view('livewire.cart-badge');
    }
}