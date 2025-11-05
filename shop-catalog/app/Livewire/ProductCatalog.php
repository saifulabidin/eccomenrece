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
        $this->categoryId = $this->sanitizeCategoryId(request('categoryId', ''));
        // Prioritize URL parameters, then use defaults
        $this->perPage = $this->sanitizePerPage(request('perPage', $this->perPage));
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
        $this->perPage = $this->sanitizePerPage($count);
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

    public function updatedCategoryId($value)
    {
        $this->categoryId = $this->sanitizeCategoryId($value);
    }

    public function updatedPerPage($value)
    {
        $this->perPage = $this->sanitizePerPage($value);
    }

    protected function sanitizeCategoryId($value)
    {
        if (is_null($value) || $value === '') {
            return '';
        }

        if (is_numeric($value)) {
            $intValue = (int) $value;
            return $intValue > 0 ? $intValue : '';
        }

        return '';
    }

    protected function sanitizePerPage($value)
    {
        if (is_null($value) || $value === '') {
            return 4;
        }

        if (! is_numeric($value)) {
            return 4;
        }

        $intValue = (int) $value;

        if ($intValue <= 0) {
            return 4;
        }

        // Optional: enforce sane upper bound to avoid abuse
        return min($intValue, 48);
    }

    public function render()
    {
        $query = Product::with(['category', 'activeVariants', 'variantAttributes'])->where('status', 'published');

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

        $categoryFilter = $this->sanitizeCategoryId($this->categoryId);
        if ($categoryFilter !== '') {
            $query->where('category_id', $categoryFilter);
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
    if ($categoryFilter !== '') $queryParams['categoryId'] = $categoryFilter;
    $perPageValue = $this->sanitizePerPage($this->perPage);
    if ($perPageValue != 4) $queryParams['perPage'] = $perPageValue;

        $path = '/katalog' . (count($queryParams) > 0 ? '?' . http_build_query($queryParams) : '');

    $products = $query->paginate($perPageValue)->withPath($path);
        $categories = Category::orderBy('name')->get();

        return view('livewire.product-catalog', compact('products', 'categories'));
    }
}
