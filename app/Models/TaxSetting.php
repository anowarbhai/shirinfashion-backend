<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $fillable = [
        'tax_enabled',
        'tax_name',
        'tax_type',
        'tax_value',
        'tax_price_type',
    ];

    protected $casts = [
        'tax_enabled' => 'boolean',
        'tax_value' => 'decimal:2',
    ];

    public static function getSettings()
    {
        $settings = self::first();
        if (! $settings) {
            $settings = self::create([
                'tax_enabled' => true,
                'tax_name' => 'VAT',
                'tax_type' => 'percentage',
                'tax_value' => 5,
                'tax_price_type' => 'exclusive',
            ]);
        }

        return $settings;
    }

    public function calculateTax($amount)
    {
        if (! $this->tax_enabled) {
            return 0;
        }

        if ($this->tax_type === 'percentage') {
            if ($this->tax_price_type === 'inclusive') {
                return ($amount * $this->tax_value) / (100 + $this->tax_value);
            }

            return ($amount * $this->tax_value) / 100;
        }

        return $this->tax_value;
    }

    public function getBaseAmount($amountWithTax)
    {
        if (! $this->tax_enabled) {
            return $amountWithTax;
        }

        if ($this->tax_type === 'percentage' && $this->tax_price_type === 'inclusive') {
            return ($amountWithTax * 100) / (100 + $this->tax_value);
        }

        return $amountWithTax;
    }

    public function getTaxFromAmount($amountWithTax)
    {
        if (! $this->tax_enabled) {
            return 0;
        }

        if ($this->tax_type === 'percentage' && $this->tax_price_type === 'inclusive') {
            return ($amountWithTax * $this->tax_value) / (100 + $this->tax_value);
        }

        if ($this->tax_type === 'percentage') {
            return ($amountWithTax * $this->tax_value) / 100;
        }

        return $this->tax_value;
    }
}
