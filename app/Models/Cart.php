<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
        'attributes',
        'price',
        'volume_tier_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'attributes' => 'array',
        'price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function volumeTier()
    {
        return $this->belongsTo(\App\Models\VolumeDiscount::class, 'volume_tier_id');
    }
}
