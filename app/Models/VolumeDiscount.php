<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolumeDiscount extends Model
{
    protected $fillable = [
        'product_id',
        'free_product_id',
        'quantity',
        'flat_price',
        'label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'flat_price' => 'decimal:2',
        'is_active' => 'boolean',
        'quantity' => 'integer',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function freeProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'free_product_id');
    }
}
