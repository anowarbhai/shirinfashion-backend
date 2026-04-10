<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    protected $fillable = [
        'api_key',
        'sender_id',
        'environment',
        'is_active',
        'order_status_sms',
        'order_placement_sms',
        'admin_login_otp',
        'customer_login_otp',
        'order_placement_template',
        'order_status_template',
        'otp_template',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order_status_sms' => 'boolean',
        'order_placement_sms' => 'boolean',
        'admin_login_otp' => 'boolean',
        'customer_login_otp' => 'boolean',
    ];

    /**
     * Get SMS settings (create default if not exists)
     */
    public static function getSettings(): self
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'environment' => 'sandbox',
                'is_active' => false,
                'order_status_sms' => false,
                'order_placement_sms' => false,
                'admin_login_otp' => false,
                'customer_login_otp' => false,
                'order_placement_template' => 'Dear {customer_name}, Your order #{order_number} has been placed successfully. Amount: ৳{total}. Thank you for shopping with us!',
                'order_status_template' => 'Dear {customer_name}, Your order #{order_number} status has been updated to: {status}. Thank you!',
                'otp_template' => 'Your OTP is {otp}. Valid for 5 minutes. Do not share this code.',
            ]);
        }
        
        return $settings;
    }

    /**
     * Get API base URL based on environment
     */
    public function getApiUrl(): string
    {
        return $this->environment === 'live' 
            ? 'https://sms.onecodesoft.com/api/'
            : 'https://sms.onecodesoft.com/api/sandbox/';
    }
}
