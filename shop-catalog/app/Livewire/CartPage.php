<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StoreConfig;
use Livewire\Component;

class CartPage extends Component
{
    public $cart = [];
    public $total = 0;
    public $itemToRemove = null;
    public $customerName = '';
    public $customerAddress = '';
    public $showForm = false;

    // Variant selection properties
    public $showVariantModal = false;
    public $editingCartItem = null;
    public $editingProduct = null;
    public $selectedSize = null;
    public $selectedColor = null;
    public $selectedVariant = null;

    public function confirmRemove($cartKey)
    {
        $this->itemToRemove = $cartKey;
    }

    protected $listeners = ['add-to-cart' => 'addToCart'];

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

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session('cart', []);
        $this->calculateTotal();
    }

    public function addToCart($data)
    {
        $productId = $data['productId'];
        $variantId = $data['variantId'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        $product = Product::find($productId);
        if (!$product) return;

        $cart = session('cart', []);
        $cartKey = $variantId ? $productId . '_' . $variantId : $productId;

        $itemName = $product->name;
        $itemPrice = $product->price;
        $itemImage = $product->images[0] ?? null;

        if ($variantId) {
            $variant = $product->variants()->find($variantId);
            if ($variant) {
                $itemName .= ' - ' . $variant->display;
                $itemPrice = $variant->final_price;
                $itemImage = $variant->images[0] ?? $itemImage;
            }
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'id' => $product->id,
                'variant_id' => $variantId,
                'name' => $itemName,
                'price' => $itemPrice,
                'quantity' => $quantity,
                'image' => $itemImage,
                'has_variants' => $variantId !== null,
            ];
        }

        session(['cart' => $cart]);
        $this->loadCart();

        $this->dispatch('cart-updated');
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function updateQuantity($cartKey, $quantity)
    {
        $cart = session('cart', []);

        if ($quantity <= 0) {
            unset($cart[$cartKey]);
        } else {
            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] = $quantity;
            }
        }

        session(['cart' => $cart]);
        $this->loadCart();
    }

    public function incrementQuantity($cartKey)
    {
        $cart = session('cart', []);

        if (isset($cart[$cartKey])) {
            // Get product and variant to check stock
            $productId = is_numeric($cartKey) ? $cartKey : explode('_', $cartKey)[0];
            $variantId = is_numeric($cartKey) ? null : explode('_', $cartKey)[1] ?? null;
            
            $product = Product::find($productId);
            if (!$product) {
                session()->flash('error', 'Produk tidak ditemukan!');
                return;
            }

            // Check stock limit
            $availableStock = $product->stock;
            if ($variantId) {
                $variant = $product->variants()->find($variantId);
                if ($variant) {
                    $availableStock = $variant->stock;
                }
            }

            if ($cart[$cartKey]['quantity'] >= $availableStock) {
                session()->flash('error', 'Stok tidak mencukupi! Maksimal ' . $availableStock . ' pcs');
                return;
            }

            $cart[$cartKey]['quantity']++;
            session(['cart' => $cart]);
            $this->loadCart();
        }
    }

    public function decrementQuantity($cartKey)
    {
        $cart = session('cart', []);

        if (isset($cart[$cartKey])) {
            if ($cart[$cartKey]['quantity'] > 1) {
                $cart[$cartKey]['quantity']--;
            } else {
                unset($cart[$cartKey]);
            }
            session(['cart' => $cart]);
            $this->loadCart();
        }
    }

    public function clearCart()
    {
        session(['cart' => []]);
        $this->loadCart();
        session()->flash('success', 'Keranjang berhasil dikosongkan!');
    }

    public function removeFromCart()
    {
        if (!$this->itemToRemove) {
            session()->flash('error', 'Tidak ada produk yang dipilih untuk dihapus!');
            return;
        }

        $cart = session('cart', []);
        $productName = $cart[$this->itemToRemove]['name'] ?? 'Produk';
        unset($cart[$this->itemToRemove]);
        session(['cart' => $cart]);
        $this->loadCart();

        // Reset the item to remove
        $this->itemToRemove = null;

        session()->flash('success', "{$productName} berhasil dihapus dari keranjang!");
    }

    public function editVariant($cartKey)
    {
        // Debug: Log the method call
        info('editVariant called with cartKey: ' . $cartKey);

        $cart = session('cart', []);
        if (!isset($cart[$cartKey])) {
            warning('Cart item not found for key: ' . $cartKey);
            return;
        }

        $cartItem = $cart[$cartKey];
        if (!$cartItem['variant_id']) {
            info('Cart item has no variant_id, skipping');
            return; // Only for variant products
        }

        $product = Product::find($cartItem['id']);
        if (!$product) {
            warning('Product not found for id: ' . $cartItem['id']);
            return;
        }

        info('Found product: ' . $product->name);

        // Load the product with variant relationships
        $product->load(['variants.combinations.variantAttribute', 'variantAttributes']);

        // Set editing item and product
        $this->editingCartItem = $cartKey;
        $this->editingProduct = $product;
        info('Set editingCartItem: ' . $cartKey . ', editingProduct: ' . $product->name);

        // Reset selection
        $this->selectedSize = null;
        $this->selectedColor = null;
        $this->selectedVariant = null;

        // Set current variant as selected
        $currentVariant = $product->variants()->find($cartItem['variant_id']);
        if ($currentVariant) {
            $this->selectedVariant = $currentVariant;
            info('Found current variant: ' . $currentVariant->name);

            // Set size and color based on current variant
            foreach ($currentVariant->combinations as $combination) {
                if ($combination->variantAttribute->attribute_type === 'size') {
                    $this->selectedSize = $combination->attribute_value;
                    info('Set selectedSize: ' . $combination->attribute_value);
                } elseif ($combination->variantAttribute->attribute_type === 'color') {
                    $this->selectedColor = $combination->attribute_value;
                    info('Set selectedColor: ' . $combination->attribute_value);
                }
            }
        } else {
            warning('Current variant not found: ' . $cartItem['variant_id']);
        }

        $this->showVariantModal = true;
        info('Set showVariantModal to true');
    }

    public function updateSelectedVariant()
    {
        if (!$this->editingProduct) {
            return;
        }

        if (!$this->editingProduct->has_variants) {
            $this->selectedVariant = null;
            return;
        }

        $variants = $this->editingProduct->variants()->active()->inStock()->get();

        foreach ($variants as $variant) {
            $sizeMatch = !$this->selectedSize || $variant->combinations->contains(function ($combination) {
                return $combination->variantAttribute->attribute_type === 'size' &&
                       $combination->attribute_value === $this->selectedSize;
            });

            $colorMatch = !$this->selectedColor || $variant->combinations->contains(function ($combination) {
                return $combination->variantAttribute->attribute_type === 'color' &&
                       $combination->attribute_value === $this->selectedColor;
            });

            $sizeRequired = $this->editingProduct->variantAttributes()->where('attribute_type', 'size')->exists();
            $colorRequired = $this->editingProduct->variantAttributes()->where('attribute_type', 'color')->exists();

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

    public function updatedSelectedSize()
    {
        $this->updateSelectedVariant();
    }

    public function updatedSelectedColor()
    {
        $this->updateSelectedVariant();
    }

    public function saveVariantChange()
    {
        if (!$this->editingCartItem || !$this->selectedVariant) {
            session()->flash('error', 'Silakan pilih variant terlebih dahulu!');
            return;
        }

        $cart = session('cart', []);
        if (!isset($cart[$this->editingCartItem])) {
            return;
        }

        $oldItem = $cart[$this->editingCartItem];
        $product = Product::find($oldItem['id']);
        if (!$product) {
            return;
        }

        // Remove old cart item
        unset($cart[$this->editingCartItem]);

        // Add new cart item with new variant
        $newCartKey = $product->id . '_' . $this->selectedVariant->id;

        $cart[$newCartKey] = [
            'id' => $product->id,
            'variant_id' => $this->selectedVariant->id,
            'name' => $product->name . ' - ' . $this->selectedVariant->display,
            'price' => $this->selectedVariant->final_price,
            'quantity' => $oldItem['quantity'],
            'image' => $this->selectedVariant->images[0] ?? $product->images[0] ?? null,
            'has_variants' => true,
        ];

        session(['cart' => $cart]);
        $this->loadCart();
        $this->dispatch('cart-updated');

        $this->showVariantModal = false;
        $this->editingCartItem = null;
        $this->editingProduct = null;
        $this->selectedSize = null;
        $this->selectedColor = null;
        $this->selectedVariant = null;

        session()->flash('success', 'Variant berhasil diubah!');
    }

    public function cancelVariantChange()
    {
        $this->showVariantModal = false;
        $this->editingCartItem = null;
        $this->editingProduct = null;
        $this->selectedSize = null;
        $this->selectedColor = null;
        $this->selectedVariant = null;
    }

    // Computed properties for variant selection
    public function getAvailableSizesProperty()
    {
        if (!$this->editingProduct || !$this->selectedColor) {
            return $this->editingProduct->available_variant_sizes ?? [];
        }

        $sizes = $this->editingProduct->variants()
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
        if (!$this->editingProduct || !$this->selectedSize) {
            return $this->editingProduct->available_variant_colors ?? [];
        }

        $colors = $this->editingProduct->variants()
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

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['quantity'];
        }
    }

    public function proceedToCheckout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        $this->showForm = true;
    }

    public function cancelCheckout()
    {
        $this->showForm = false;
        $this->reset(['customerName', 'customerAddress']);
    }

    public function checkout()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        $storeConfig = StoreConfig::first();
        $whatsappNumber = $storeConfig->whatsapp_number ?? '6282242034791';

        $message = "PESANAN BARU\n\n";
        $message .= "Data Pelanggan:\n";
        $message .= "Nama: {$this->customerName}\n";
                $message .= "Alamat: {$this->customerAddress}\n\n";
        $message .= "Detail Pesanan:\n";

        foreach ($this->cart as $item) {
            $message .= "• {$item['name']} (Qty: {$item['quantity']}) - Rp " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "\n";
        }

        $message .= "\nTotal Pembayaran: Rp " . number_format($this->total, 0, ',', '.') . "\n\n";
        $message .= "Mohon konfirmasi pesanan saya. Terima kasih!";

        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);

        // Clear cart after checkout
        session(['cart' => []]);
        $this->cart = [];
        $this->total = 0;
        $this->showForm = false;
        $this->reset(['customerName', 'customerAddress']);

        return redirect($whatsappUrl);
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}