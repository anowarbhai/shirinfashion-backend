<?php

namespace App\Services;

use App\Models\SmsSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private SmsSetting $settings;

    public function __construct()
    {
        $this->settings = SmsSetting::getSettings();
    }

    /**
     * Check if SMS service is active
     */
    public function isActive(): bool
    {
        return $this->settings->is_active && !empty($this->settings->api_key);
    }

    /**
     * Send SMS to a single number
     */
    public function sendSms(string $number, string $message): bool
    {
        if (!$this->isActive()) {
            Log::warning('SMS service is not active');
            return false;
        }

        try {
            // Format number (ensure it starts with 880)
            $number = $this->formatNumber($number);

            $url = $this->settings->getApiUrl() . 'send-sms';
            
            $response = Http::get($url, [
                'api_key' => $this->settings->api_key,
                'type' => 'text',
                'number' => $number,
                'senderid' => $this->settings->sender_id,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('SMS sent successfully', ['number' => $number, 'response' => $data]);
                return true;
            }

            Log::error('SMS sending failed', ['number' => $number, 'response' => $response->body()]);
            return false;

        } catch (\Exception $e) {
            Log::error('SMS sending error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk SMS
     */
    public function sendBulkSms(array $messages): bool
    {
        if (!$this->isActive()) {
            Log::warning('SMS service is not active');
            return false;
        }

        try {
            $url = $this->settings->getApiUrl() . 'send-bulk-sms';
            
            // Format numbers
            $formattedMessages = array_map(function($msg) {
                $msg['Number'] = $this->formatNumber($msg['Number']);
                return $msg;
            }, $messages);

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, [
                'api_key' => $this->settings->api_key,
                'senderid' => $this->settings->sender_id,
                'MessageParameters' => $formattedMessages,
            ]);

            if ($response->successful()) {
                Log::info('Bulk SMS sent successfully');
                return true;
            }

            Log::error('Bulk SMS sending failed', ['response' => $response->body()]);
            return false;

        } catch (\Exception $e) {
            Log::error('Bulk SMS sending error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order placement SMS
     */
    public function sendOrderPlacementSms($order): bool
    {
        if (!$this->settings->order_placement_sms) {
            return false;
        }

        $template = $this->settings->order_placement_template;
        $message = $this->parseTemplate($template, [
            'customer_name' => $order->customer_name,
            'order_number' => $order->order_number,
            'total' => number_format($order->total, 2),
        ]);

        return $this->sendSms($order->customer_phone, $message);
    }

    /**
     * Send order status update SMS
     */
    public function sendOrderStatusSms($order): bool
    {
        if (!$this->settings->order_status_sms) {
            return false;
        }

        $template = $this->settings->order_status_template;
        $message = $this->parseTemplate($template, [
            'customer_name' => $order->customer_name,
            'order_number' => $order->order_number,
            'status' => $order->status,
        ]);

        return $this->sendSms($order->customer_phone, $message);
    }

    /**
     * Send OTP SMS
     */
    public function sendOtpSms(string $number, string $otp): bool
    {
        if (!$this->settings->is_active) {
            return false;
        }

        $template = $this->settings->otp_template;
        $message = $this->parseTemplate($template, [
            'otp' => $otp,
        ]);

        return $this->sendSms($number, $message);
    }

    /**
     * Check SMS balance
     */
    public function checkBalance(): ?array
    {
        if (!$this->isActive()) {
            return null;
        }

        try {
            $url = $this->settings->getApiUrl() . 'get-balance';
            
            $response = Http::get($url, [
                'api_key' => $this->settings->api_key,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Balance check error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format phone number (ensure starts with 880)
     */
    private function formatNumber(string $number): string
    {
        // Remove any non-digit characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // If starts with 0, replace with 880
        if (substr($number, 0, 1) === '0') {
            $number = '880' . substr($number, 1);
        }
        
        // If doesn't start with 880, add it
        if (substr($number, 0, 3) !== '880') {
            $number = '880' . $number;
        }
        
        return $number;
    }

    /**
     * Parse template variables
     */
    private function parseTemplate(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
}
