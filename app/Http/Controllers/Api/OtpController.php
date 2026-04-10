<?php

namespace App\Http\Controllers\Api;

use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtpController extends BaseController
{
    private OtpService $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService();
    }

    /**
     * Send OTP
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'type' => 'required|in:admin,customer',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $result = $this->otpService->generateAndSendOtp(
            $request->phone,
            $request->type
        );

        if ($result['success']) {
            return $this->success([
                'otp_required' => $result['otp_required'],
                'message' => $result['message'],
                'otp' => $result['otp'] ?? null,
            ]);
        }

        return $this->error($result['message'], 500);
    }

    /**
     * Verify OTP
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:20',
            'otp' => 'required|string|size:6',
            'type' => 'required|in:admin,customer',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $isValid = $this->otpService->verifyOtp(
            $request->phone,
            $request->otp,
            $request->type
        );

        if ($isValid) {
            return $this->success(['verified' => true], 'OTP verified successfully');
        }

        return $this->error('Invalid or expired OTP', 400);
    }

    /**
     * Check if OTP is required
     */
    public function checkRequired(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:admin,customer',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        $required = $this->otpService->isOtpRequired($request->type);

        return $this->success(['otp_required' => $required]);
    }
}
