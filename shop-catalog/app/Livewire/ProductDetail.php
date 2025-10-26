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
    public $showFullDescription = false;
    public $selectedVariant = null;
    public $selectedSize = null;
    public $selectedColor = null;

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $this->product->loadVariantData();
    }

    public function toggleDescription()
    {
        $this->showFullDescription = !$this->showFullDescription;
    }

    public function getDisplayDescriptionProperty()
    {
        if (!$this->product->description) {
            return '';
        }

        $plainText = strip_tags($this->product->description);
        $isLong = strlen($plainText) > 150;

        if (!$isLong || $this->showFullDescription) {
            return $this->product->description;
        }

        // Truncate to ~150 characters, preserving HTML
        $truncated = substr($plainText, 0, 150);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }

    public function getNeedsTruncationProperty()
    {
        if (!$this->product->description) {
            return false;
        }

        $plainText = strip_tags($this->product->description);
        return strlen($plainText) > 150;
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
        $maxStock = $this->current_stock ?? 999;
        if ($this->quantity < $maxStock) {
            $this->quantity++;
        } else {
            session()->flash('error', 'Jumlah barang melebihi stok yang tersedia! Stok: ' . $maxStock . ' pcs');
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function updatedSelectedSize()
    {
        $this->updateSelectedVariant();
        // Reset quantity to 1 when variant changes
        $this->quantity = 1;
    }

    public function updatedSelectedColor()
    {
        $this->updateSelectedVariant();
        // Reset quantity to 1 when variant changes
        $this->quantity = 1;
    }

    public function updatedQuantity($value)
    {
        $maxStock = $this->current_stock ?? 999;
        if ($value > $maxStock) {
            $this->quantity = $maxStock;
            session()->flash('error', 'Jumlah barang melebihi stok yang tersedia! Stok: ' . $maxStock . ' pcs');
        } elseif ($value < 1) {
            $this->quantity = 1;
        }
    }

    public function updateSelectedVariant()
    {
        if (!$this->product->has_variants) {
            $this->selectedVariant = null;
            return;
        }

        $variants = $this->product->variants()->active()->inStock()->get();

        foreach ($variants as $variant) {
            $sizeMatch = !$this->selectedSize || $variant->combinations->contains(function ($combination) {
                return $combination->variantAttribute->attribute_type === 'size' &&
                       $combination->attribute_value === $this->selectedSize;
            });

            $colorMatch = !$this->selectedColor || $variant->combinations->contains(function ($combination) {
                return $combination->variantAttribute->attribute_type === 'color' &&
                       $combination->attribute_value === $this->selectedColor;
            });

            $sizeRequired = $this->product->variantAttributes()->where('attribute_type', 'size')->exists();
            $colorRequired = $this->product->variantAttributes()->where('attribute_type', 'color')->exists();

            if ($sizeMatch && $colorMatch) {
                $hasRequiredAttributes = true;
                if ($sizeRequired && !$this->selectedSize) $hasRequiredAttributes = false;
                if ($colorRequired && !$this->selectedColor) $hasRequiredAttributes = false;

                if ($hasRequiredAttributes) {
                    $this->selectedVariant = $variant;
                    return;
                }
            }
        }

        $this->selectedVariant = null;
    }

    public function getAvailableSizesProperty()
    {
        if (!$this->selectedColor || !$this->product->has_variants) {
            return $this->product->available_variant_sizes;
        }

        $sizes = $this->product->variants()
            ->whereHas('combinations', function ($query) {
                $query->where('attribute_value', $this->selectedColor)
                      ->whereHas('variantAttribute', function ($q) {
                          $q->where('attribute_type', 'color');
                      });
            })
            ->active()
            ->inStock()
            ->get()
            ->flatMap(function ($variant) {
                return $variant->combinations
                    ->filter(function ($combination) {
                        return $combination->variantAttribute->attribute_type === 'size';
                    })
                    ->pluck('attribute_value');
            })
            ->unique()
            ->values()
            ->toArray();

        // Sort sizes alphabetically to maintain consistent ordering
        sort($sizes);
        return $sizes;
    }

    public function getAvailableColorsProperty()
    {
        if (!$this->selectedSize || !$this->product->has_variants) {
            return $this->product->available_variant_colors;
        }

        $colors = $this->product->variants()
            ->whereHas('combinations', function ($query) {
                $query->where('attribute_value', $this->selectedSize)
                      ->whereHas('variantAttribute', function ($q) {
                          $q->where('attribute_type', 'size');
                      });
            })
            ->active()
            ->inStock()
            ->get()
            ->flatMap(function ($variant) {
                return $variant->combinations
                    ->filter(function ($combination) {
                        return $combination->variantAttribute->attribute_type === 'color';
                    })
                    ->pluck('attribute_value');
            })
            ->unique()
            ->values()
            ->toArray();

        // Sort colors alphabetically to maintain consistent ordering
        sort($colors);
        return $colors;
    }

    public function getCurrentPriceProperty()
    {
        if ($this->product->has_variants && $this->selectedVariant) {
            return $this->selectedVariant->final_price;
        }

        return $this->product->discount_price ?? $this->product->price;
    }

    public function getPriceRangeProperty()
    {
        if (!$this->product->has_variants) {
            return null;
        }

        $variants = $this->product->variants()->active()->inStock()->get();
        if ($variants->isEmpty()) {
            return null;
        }

        $prices = $variants->pluck('final_price');
        $minPrice = $prices->min();
        $maxPrice = $prices->max();

        if ($minPrice === $maxPrice) {
            return 'Rp ' . number_format($minPrice, 0, ',', '.');
        }

        return 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
    }

    public function getCurrentStockProperty()
    {
        // Force fresh data from database to avoid cache issues
        if ($this->product->has_variants && $this->selectedVariant) {
            // Refresh variant from database to get latest stock
            $freshVariant = $this->product->variants()->find($this->selectedVariant->id);
            return $freshVariant ? $freshVariant->stock : 0;
        }

        return $this->product->stock ?? 999;
    }

    public function checkoutNow()
    {
        $this->validate();

        $productName = $this->product->name;
        if ($this->product->has_variants && $this->selectedVariant) {
            $productName .= ' - ' . $this->selectedVariant->display;
        }

        $message = "PESANAN BARU\n\n";
        $message .= "Data Pelanggan:\n";
        $message .= "Nama: {$this->customerName}\n";
        $message .= "Alamat: {$this->customerAddress}\n\n";
        $message .= "Detail Pesanan:\n";
        $message .= "• {$productName} (Qty: {$this->quantity}) - Rp " . number_format($this->current_price * $this->quantity, 0, ',', '.') . "\n";
        $message .= "\nTotal Pembayaran: Rp " . number_format($this->current_price * $this->quantity, 0, ',', '.') . "\n\n";
        $message .= "Mohon konfirmasi pesanan saya. Terima kasih!";

        $config = StoreConfig::first() ?? (object)['whatsapp_number' => '6281234567890'];
        $whatsappNumber = $config->whatsapp_number;

        $this->showForm = false;
        $this->reset(['customerName', 'customerAddress']);

        return redirect()->away("https://api.whatsapp.com/send?phone={$whatsappNumber}&text=" . urlencode($message));
    }

    public function addToCart()
    {
        if ($this->product->has_variants && !$this->selectedVariant) {
            session()->flash('error', 'Silakan pilih variant produk terlebih dahulu!');
            return;
        }

        $maxStock = $this->current_stock ?? 999;

        // Check if adding to cart exceeds stock
        $cart = session('cart', []);
        $cartKey = $this->product->has_variants
            ? $this->product->id . '_' . $this->selectedVariant->id
            : $this->product->id;

        $currentCartQuantity = $cart[$cartKey]['quantity'] ?? 0;
        $requestedTotal = $currentCartQuantity + $this->quantity;

        if ($requestedTotal > $maxStock) {
            session()->flash('error', 'Jumlah barang melebihi stok yang tersedia! Stok: ' . $maxStock . ' pcs, sudah ada ' . $currentCartQuantity . ' pcs di keranjang');
            return;
        }

        $itemName = $this->product->name;
        if ($this->product->has_variants && $this->selectedVariant) {
            $itemName .= ' - ' . $this->selectedVariant->display;
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $this->quantity;
        } else {
            $cart[$cartKey] = [
                'id' => $this->product->id,
                'variant_id' => $this->selectedVariant?->id,
                'name' => $itemName,
                'price' => $this->current_price,
                'quantity' => $this->quantity,
                'image' => $this->selectedVariant?->images[0] ?? $this->product->images[0] ?? null,
                'has_variants' => $this->product->has_variants,
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
