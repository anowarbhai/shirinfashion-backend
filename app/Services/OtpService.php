<?php

namespace App\Services;

use App\Models\SmsSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpService
{
    private SmsService $smsService;

    public function __construct()
    {
        $this->smsService = new SmsService();
    }

    /**
     * Generate and send OTP
     */
    public function generateAndSendOtp(string $phone, string $type = 'login'): array
    {
        $settings = SmsSetting::getSettings();
        
        // Check if OTP is enabled for this type
        if ($type === 'admin' && !$settings->admin_login_otp) {
            return ['success' => true, 'otp_required' => false];
        }
        
        if ($type === 'customer' && !$settings->customer_login_otp) {
            return ['success' => true, 'otp_required' => false];
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 5 minutes
        $cacheKey = "otp_{$type}_" . $this->sanitizePhone($phone);
        Cache::put($cacheKey, $otp, 300); // 5 minutes

        // Send OTP via SMS
        $success = $this->smsService->sendOtpSms($phone, $otp);

        if ($success) {
            Log::info("OTP sent successfully", ['phone' => $phone, 'type' => $type]);
            return [
                'success' => true,
                'otp_required' => true,
                'message' => 'OTP sent successfully',
                'otp' => config('app.debug') ? $otp : null // Show OTP only in debug mode
            ];
        }

        Log::error("Failed to send OTP", ['phone' => $phone, 'type' => $type]);
        return [
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.'
        ];
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(string $phone, string $otp, string $type = 'login'): bool
    {
        $cacheKey = "otp_{$type}_" . $this->sanitizePhone($phone);
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp) {
            return false;
        }

        if ($storedOtp === $otp) {
            // Clear OTP after successful verification
            Cache::forget($cacheKey);
            return true;
        }

        return false;
    }

    /**
     * Check if OTP is required for login
     */
    public function isOtpRequired(string $type): bool
    {
        $settings = SmsSetting::getSettings();
        
        if (!$settings->is_active) {
            return false;
        }

        if ($type === 'admin') {
            return $settings->admin_login_otp;
        }

        if ($type === 'customer') {
            return $settings->customer_login_otp;
        }

        return false;
    }

    /**
     * Sanitize phone number for cache key
     */
    private function sanitizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
