<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_type',
        'attribute_name',
        'attribute_values',
        'is_active',
    ];

    protected $casts = [
        'attribute_values' => 'array',
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

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'variant_combinations')
            ->withPivot('attribute_value');
    }

    public function getDisplayValuesAttribute()
    {
        return is_array($this->attribute_values) ? $this->attribute_values : [];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('attribute_type', $type);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
