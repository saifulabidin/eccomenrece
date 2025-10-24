<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class CategoryList extends Component
{
    public function render()
    {
        $categories = Category::all();

        return view('livewire.category-list', compact('categories'));
    }
}
