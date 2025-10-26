<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'category_id', 'price', 'discount_price', 'stock', 'images', 'status', 'has_variants', 'base_sku'
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'has_variants' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function variantAttributes()
    {
        return $this->hasMany(VariantAttribute::class);
    }

    public function activeVariants()
    {
        return $this->variants()->active()->inStock();
    }

    public function getHasVariantsAttribute()
    {
        return (bool) ($this->attributes['has_variants'] ?? false);
    }

    public function getDisplayPriceAttribute()
    {
        if ($this->getHasVariantsAttribute()) {
            $variants = $this->activeVariants()->get();
            if ($variants->count() > 0) {
                $minPrice = $variants->min(function ($variant) {
                    return $variant->final_price;
                });
                $maxPrice = $variants->max(function ($variant) {
                    return $variant->final_price;
                });

                if ($minPrice && $maxPrice) {
                    return $minPrice == $maxPrice
                        ? 'Rp ' . number_format($minPrice, 0, ',', '.')
                        : 'Rp ' . number_format($minPrice, 0, ',', '.') . ' - Rp ' . number_format($maxPrice, 0, ',', '.');
                }
            }

            return 'Rp ' . number_format($this->price, 0, ',', '.');
        }

        return $this->final_price_formatted;
    }

    public function getAvailableVariantSizesAttribute()
    {
        if (!$this->relationLoaded('variants')) {
            $this->load(['variants.combinations.variantAttribute']);
        }

        $sizes = $this->variants
            ->filter(function ($variant) {
                return $variant->is_active && $variant->stock > 0;
            })
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

    public function getAvailableVariantColorsAttribute()
    {
        if (!$this->relationLoaded('variants')) {
            $this->load(['variants.combinations.variantAttribute']);
        }

        $colors = $this->variants
            ->filter(function ($variant) {
                return $variant->is_active && $variant->stock > 0;
            })
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

    public function generateVariantSku($size, $color)
    {
        $baseSku = $this->base_sku ?: Str::upper(Str::slug($this->name));
        $sizeCode = $size ? strtoupper(str_replace([' ', '-'], '', $size)) : '';
        $colorCode = $color ? strtoupper(str_replace([' ', '-'], '', $color)) : '';

        return implode('-', array_filter([$baseSku, $sizeCode, $colorCode]));
    }

    public function createVariantCombination($attributes, $price = null, $stock = 10)
    {
        $size = $attributes['size'] ?? null;
        $color = $attributes['color'] ?? null;
        $sku = $this->generateVariantSku($size, $color);

        $name = $this->name;
        if ($size) $name .= " - {$size}";
        if ($color) $name .= " - {$color}";

        // Ensure variant has a price - use provided price first, then parent price, then default
        $variantPrice = $price ?? $this->price ?? 1000000; // Default to Rp 1.000.000 if no price is set

        // Only set discount price if it's valid (lower than regular price)
        $discountPrice = null;
        if ($this->discount_price && $this->discount_price < $variantPrice) {
            $discountPrice = $this->discount_price;
        }

        $variant = $this->variants()->create([
            'sku' => $sku,
            'name' => $name,
            'price' => $variantPrice,
            'discount_price' => $discountPrice,
            'stock' => $stock,
            'is_active' => true,
        ]);

        // Create combinations for each attribute
        foreach ($attributes as $type => $value) {
            $attribute = $this->variantAttributes()->byType($type)->first();
            if ($attribute) {
                $variant->combinations()->create([
                    'variant_attribute_id' => $attribute->id,
                    'attribute_value' => $value,
                ]);
            }
        }

        return $variant;
    }

    public function generateAllVariantCombinations()
    {
        
        $sizeAttribute = $this->variantAttributes()->byType('size')->active()->first();
        $colorAttribute = $this->variantAttributes()->byType('color')->active()->first();

        
        $sizes = [null];
        $colors = [null];

        // Extract size values with better error handling
        if ($sizeAttribute) {
            $sizeValues = $sizeAttribute->attribute_values;
            
            if (is_array($sizeValues) && !empty($sizeValues)) {
                $sizes = $sizeValues;
            } elseif (is_string($sizeValues)) {
                $decoded = json_decode($sizeValues, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $sizes = $decoded;
                } elseif (strpos($sizeValues, ',') !== false) {
                    $sizes = array_map('trim', explode(',', $sizeValues));
                }
            }
                    }

        // Extract color values with better error handling
        if ($colorAttribute) {
            $colorValues = $colorAttribute->attribute_values;
            
            if (is_array($colorValues) && !empty($colorValues)) {
                $colors = $colorValues;
            } elseif (is_string($colorValues)) {
                $decoded = json_decode($colorValues, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $colors = $decoded;
                } elseif (strpos($colorValues, ',') !== false) {
                    $colors = array_map('trim', explode(',', $colorValues));
                }
            }
                    }

        $variants = collect();
        $variantCount = 0;

        foreach ($sizes as $size) {
            foreach ($colors as $color) {
                if ($size || $color) {
                    $attributes = [];
                    if ($size) $attributes['size'] = $size;
                    if ($color) $attributes['color'] = $color;

                    try {
                        $variant = $this->createVariantCombination($attributes);
                        $variants->push($variant);
                        $variantCount++;
                    } catch (\Exception $e) {
                        // Silent fail for now - can add error handling later if needed
                    }
                }
            }
        }

        return $variants;
    }

    public function scopeWithVariants($query)
    {
        return $query->with(['variants', 'variantAttributes']);
    }

    public function scopeActiveVariantsOnly($query)
    {
        return $query->where('has_variants', true)->whereHas('variants', function ($q) {
            $q->active()->inStock();
        });
    }

    /**
     * Optimize variant loading by preloading relationships
     */
    public function loadVariantData()
    {
        return $this->load([
            'variants.combinations.variantAttribute',
            'variantAttributes' => function ($query) {
                $query->active()->orderBy('attribute_type');
            }
        ]);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the approved reviews for the product.
     */
    public function approvedReviews()
    {
        return $this->reviews()->approved();
    }

    /**
     * Get the average rating for the product.
     */
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of approved reviews.
     */
    public function getTotalReviewsAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get the formatted average rating (1 decimal).
     */
    public function getFormattedAverageRatingAttribute()
    {
        return number_format($this->average_rating, 1);
    }

    /**
     * Get the star rating display for the product.
     */
    public function getStarsAttributeAttribute(): string
    {
        $averageRating = round($this->average_rating);
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $averageRating) {
                $stars .= '<i class="bi bi-star-fill"></i>';
            } else {
                $stars .= '<i class="bi bi-star"></i>';
            }
        }
        return $stars;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
