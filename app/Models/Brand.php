<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if (! $this->logo) {
            return null;
        }

        // Remove leading slashes and 'storage/' prefix if they exist
        $logo = $this->logo;
        $logo = ltrim($logo, '/');
        if (strpos($logo, 'storage/') === 0) {
            $logo = substr($logo, 8); // Remove 'storage/' prefix
        }

        return asset('storage/'.$logo);
    }
}
