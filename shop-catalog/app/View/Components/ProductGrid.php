<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductGrid extends Component
{
    public $products;
    public $columns;

    /**
     * Create a new component instance.
     */
    public function __construct($products, $columns = 'col-md-4')
    {
        $this->products = $products;
        $this->columns = $columns;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.product-grid');
    }
}
