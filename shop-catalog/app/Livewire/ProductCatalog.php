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
        $query = Product::with('category')->where('status', 'published');

        if ($this->search) {
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        $query->orderBy('created_at', 'desc');

        $products = $query->paginate(12);
        $categories = Category::orderBy('name')->get();

        return view('livewire.product-catalog', compact('products', 'categories'));
    }
}
