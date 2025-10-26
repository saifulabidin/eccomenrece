<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VariantCombination extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'variant_attribute_id',
        'attribute_value',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($combination) {
            // Update the variant's SKU when combination changes
            $variant = $combination->productVariant;
            if ($variant && $variant->product) {
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

                $variant->sku = $variant->product->generateVariantSku($sizeValue, $colorValue);
                $variant->save();
            }
        });

        static::deleted(function ($combination) {
            // Update the variant's SKU when combination is removed
            $variant = $combination->productVariant;
            if ($variant && $variant->product) {
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

                $variant->sku = $variant->product->generateVariantSku($sizeValue, $colorValue);
                $variant->save();
            }
        });
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function variantAttribute()
    {
        return $this->belongsTo(VariantAttribute::class);
    }

    public function scopeForVariant($query, $variantId)
    {
        return $query->where('product_variant_id', $variantId);
    }

    public function scopeForAttribute($query, $attributeId)
    {
        return $query->where('variant_attribute_id', $attributeId);
    }
}
