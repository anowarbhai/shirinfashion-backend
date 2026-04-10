<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['image_url'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getActiveProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('is_active', true);
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
