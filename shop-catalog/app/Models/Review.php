<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'google_id',
        'user_name',
        'user_email',
        'user_avatar',
        'rating',
        'review',
        'approved',
        'ip_address',
    ];

    protected $casts = [
        'rating' => 'integer',
        'approved' => 'boolean',
    ];

    /**
     * Get the product that owns the review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope a query to only include pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('approved', false);
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to order by highest rating.
     */
    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    /**
     * Get the formatted rating stars.
     */
    public function getStarsAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="bi bi-star-fill"></i>';
            } else {
                $stars .= '<i class="bi bi-star"></i>';
            }
        }
        return $stars;
    }

    /**
     * Get the formatted created date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('d M Y');
    }

    /**
     * Check if the user has verified badge.
     */
    public function getIsVerifiedAttribute(): bool
    {
        return !empty($this->google_id) && !empty($this->user_email);
    }

    /**
     * Get display name (fallback to user_name if Google name not available).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->user_name ?? 'Anonymous';
    }
}
