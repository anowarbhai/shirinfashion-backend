<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sku',
        'stock_quantity',
        'manage_stock',
        'stock_status',
        'is_featured',
        'is_active',
        'reviews_enabled',
        'avg_rating_enabled',
        'average_rating',
        'review_count',
        'image',
        'images',
        'brand',
        'origin',
        'weight',
        'dimensions',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'manage_stock' => 'boolean',
        'reviews_enabled' => 'boolean',
        'avg_rating_enabled' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'images' => 'array',
        'weight' => 'integer',
    ];

    protected $appends = [
        'average_rating',
        'review_count',
        'computed_stock_status',
        'image_url',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function brandModel(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->sale_price && $this->price > $this->sale_price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }

        return null;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->attributes['average_rating'] ?? 0);
    }

    public function getReviewCountAttribute(): int
    {
        return (int) ($this->attributes['review_count'] ?? 0);
    }

    public function getComputedStockStatusAttribute(): string
    {
        // If manage_stock is disabled, always return 'in_stock'
        if (! $this->manage_stock) {
            return 'in_stock';
        }

        // If manage_stock is enabled, check stock_quantity
        if ($this->stock_quantity > 0) {
            return 'in_stock';
        }

        return 'out_of_stock';
    }

    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }

        // Remove leading slashes and 'storage/' prefix if they exist
        $image = $this->image;
        $image = ltrim($image, '/');
        if (strpos($image, 'storage/') === 0) {
            $image = substr($image, 8); // Remove 'storage/' prefix
        }

        return asset('storage/'.$image);
    }
}
