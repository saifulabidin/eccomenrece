<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class FeaturedProducts extends Component
{
    public function render()
    {
        $featuredProducts = Product::where('status', 'published')->take(6)->get();

        return view('livewire.featured-products', compact('featuredProducts'));
    }
}
