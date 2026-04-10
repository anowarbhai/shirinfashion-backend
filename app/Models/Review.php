<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'rating',
        'comment',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            static::updateProductRating($review->product_id);
        });

        static::updated(function ($review) {
            static::updateProductRating($review->product_id);
        });

        static::deleted(function ($review) {
            static::updateProductRating($review->product_id);
        });
    }

    public static function updateProductRating(int $productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $stats = static::where('product_id', $productId)
                ->where('is_active', true)
                ->selectRaw('COUNT(*) as count, AVG(rating) as avg_rating')
                ->first();

            $product->update([
                'average_rating' => round($stats->avg_rating ?? 0, 1),
                'review_count' => $stats->count ?? 0,
            ]);
        }
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
