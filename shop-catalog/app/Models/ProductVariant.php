<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'discount_price',
        'stock',
        'images',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($variant) {
            // Auto-generate SKU if empty or needs update
            if (empty($variant->sku) || $variant->isDirty('name')) {
                $sizeValue = $variant->combinations()
                    ->whereHas('variantAttribute', function ($query) {
                        $query->where('attribute_type', 'size');
                    })
                    ->value('attribute_value');

                $colorValue = $variant->combinations()
                    ->whereHas('variantAttribute', function ($query) {
                        $query->where('attribute_type', 'color');
                    })
                    ->value('attribute_value');

                if ($variant->product) {
                    $variant->sku = $variant->product->generateVariantSku($sizeValue, $colorValue);
                }
            }
        });
    }

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function combinations()
    {
        return $this->hasMany(VariantCombination::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(VariantAttribute::class, 'variant_combinations')
            ->withPivot('attribute_value');
    }

    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    public function getHasDiscountAttribute()
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->has_discount) {
            return 0;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedDiscountPriceAttribute()
    {
        return $this->discount_price ? 'Rp ' . number_format($this->discount_price, 0, ',', '.') : null;
    }

    public function getFormattedFinalPriceAttribute()
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    public function getMainImageAttribute()
    {
        return $this->images[0] ?? $this->product->images[0] ?? null;
    }

    public function getImageUrlAttribute()
    {
        return $this->main_image ? asset('storage/' . $this->main_image) : null;
    }

    public function getAllImageUrlsAttribute()
    {
        return collect($this->images)->map(function ($image) {
            return asset('storage/' . $image);
        })->filter()->values()->toArray();
    }

    public function hasCustomImages()
    {
        return !empty($this->images) && is_array($this->images);
    }

    public function getDisplayAttribute()
    {
        $attributes = $this->attributes()
            ->orderBy('attribute_type')
            ->get()
            ->map(function ($attribute) {
                return ucfirst($attribute->attribute_type) . ': ' . $attribute->pivot->attribute_value;
            })
            ->join(' - ');

        return $this->name . ($attributes ? ' - ' . $attributes : '');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
