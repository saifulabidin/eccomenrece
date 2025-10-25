<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'category_id', 'price', 'discount_price', 'stock', 'images', 'status'
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
