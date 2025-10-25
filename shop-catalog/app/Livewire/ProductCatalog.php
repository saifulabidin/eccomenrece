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
    public $showMobileFilter = false;
    public $perPage = 4;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'perPage' => ['except' => 4],
    ];

    public function mount()
    {
        $this->categoryId = request('categoryId', '');
        // Prioritize URL parameters, then use defaults
        $this->perPage = request('perPage', $this->perPage);
    }

    public function toggleMobileFilter()
    {
        $this->showMobileFilter = !$this->showMobileFilter;
    }

    public function resetAll()
    {
        $this->search = '';
        $this->categoryId = '';
        $this->resetPage();
    }

    public function updatePerPage($count)
    {
        $this->perPage = (int) $count;
        $this->resetPage();
    }

    // Getter methods for pagination view
    public function getSearch()
    {
        return $this->search;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    public function getPerPage()
    {
        return $this->perPage;
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

        if ($this->search && strlen(trim($this->search)) >= 2) {
            $searchTerm = trim($this->search);
            $query->where(function ($q) use ($searchTerm) {
                // Priority 1: Exact product name match (starts with) - case insensitive
                $q->whereRaw('LOWER(name) LIKE ?', [strtolower($searchTerm) . '%'])
                  // Priority 2: Product name contains search term - case insensitive
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  // Priority 3: Description contains search term - case insensitive
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  // Priority 4: Category name contains search term - case insensitive
                  ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                      $categoryQuery->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
                  });
            });
        }

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        // Order by relevance if search is active, otherwise by newest
        if ($this->search && strlen(trim($this->search)) >= 2) {
            $searchTerm = strtolower(trim($this->search));
            $query->orderByRaw("
                CASE
                    WHEN LOWER(name) LIKE ? THEN 1
                    WHEN LOWER(name) LIKE ? THEN 2
                    WHEN LOWER(description) LIKE ? THEN 3
                    WHEN EXISTS (SELECT 1 FROM categories c WHERE c.id = products.category_id AND LOWER(c.name) LIKE ?) THEN 4
                    ELSE 5
                END
            ", [
                $searchTerm . '%',
                '%' . $searchTerm . '%',
                '%' . $searchTerm . '%',
                '%' . $searchTerm . '%'
            ]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Build query parameters for pagination
        $queryParams = [];
        if ($this->search) $queryParams['search'] = $this->search;
        if ($this->categoryId) $queryParams['categoryId'] = $this->categoryId;
        if ($this->perPage != 4) $queryParams['perPage'] = $this->perPage;

        $path = '/katalog' . (count($queryParams) > 0 ? '?' . http_build_query($queryParams) : '');

        $products = $query->paginate($this->perPage)->withPath($path);
        $categories = Category::orderBy('name')->get();

        return view('livewire.product-catalog', compact('products', 'categories'));
    }
}
