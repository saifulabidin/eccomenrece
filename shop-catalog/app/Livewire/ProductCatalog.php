<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId = '';

    public function mount()
    {
        $this->categoryId = request('categoryId', '');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::where('status', 'published');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('livewire.product-catalog', compact('products', 'categories'));
    }
}
