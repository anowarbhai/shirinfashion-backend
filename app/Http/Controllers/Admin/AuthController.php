<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected OtpService $otpService;

    public function __construct()
    {
        $this->otpService = new OtpService;
    }

    public function showLoginForm()
    {
        // Check if we're in OTP verification step
        $otpStep = Session::get('admin_otp_step', false);
        $email = Session::get('admin_otp_email', '');
        $phone = Session::get('admin_otp_phone', '');

        return view('admin.login', compact('otpStep', 'email', 'phone'));
    }

    public function login(Request $request)
    {
        // If we're in OTP step, verify OTP
        if (Session::get('admin_otp_step')) {
            return $this->verifyOtp($request);
        }

        // First step: Validate email and password
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Invalid admin credentials.',
            ]);
        }

        // Check if user has admin access (is_admin OR has role)
        if (! $user->is_admin && ! $user->hasRole()) {
            throw ValidationException::withMessages([
                'email' => 'Invalid admin credentials.',
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid admin credentials.',
            ]);
        }

        // Check if OTP is required for admin login
        $smsSettings = SmsSetting::getSettings();

        // Debug: Log the settings
        \Log::info('SMS Settings', [
            'is_active' => $smsSettings->is_active,
            'admin_login_otp' => $smsSettings->admin_login_otp,
            'user_phone' => $user->phone,
            'api_key' => $smsSettings->api_key ? 'Set' : 'Not Set',
        ]);

        if ($smsSettings->is_active && $smsSettings->admin_login_otp) {
            if (! $user->phone) {
                return redirect()->route('admin.login')->with('error', 'OTP is enabled but your account has no phone number. Please contact support.');
            }

            // Send OTP
            $result = $this->otpService->generateAndSendOtp($user->phone, 'admin');

            if ($result['success']) {
                // Store user data in session for OTP verification
                Session::put('admin_otp_step', true);
                Session::put('admin_otp_email', $user->email);
                Session::put('admin_otp_phone', $user->phone);
                Session::put('admin_otp_user_id', $user->id);

                return redirect()->route('admin.login')->with('otp_sent', true);
            } else {
                return redirect()->route('admin.login')->with('error', 'Failed to send OTP. Error: '.($result['message'] ?? 'Unknown error'));
            }
        }

        // If OTP is not enabled or user has no phone, login directly
        Auth::login($user);
        $request->session()->regenerate();
        Session::forget(['admin_otp_step', 'admin_otp_email', 'admin_otp_phone', 'admin_otp_user_id']);

        return redirect()->intended('/admin/dashboard');
    }

    private function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $phone = Session::get('admin_otp_phone');
        $otp = $request->otp;

        $isValid = $this->otpService->verifyOtp($phone, $otp, 'admin');

        if (! $isValid) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid or expired OTP.',
            ]);
        }

        // OTP verified, login user
        $userId = Session::get('admin_otp_user_id');
        $user = \App\Models\User::find($userId);

        if ($user && $user->is_admin) {
            Auth::login($user);
            $request->session()->regenerate();
            Session::forget(['admin_otp_step', 'admin_otp_email', 'admin_otp_phone', 'admin_otp_user_id']);

            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->route('admin.login')->with('error', 'User not found.');
    }

    public function resendOtp(Request $request)
    {
        if (! Session::get('admin_otp_step')) {
            return redirect()->route('admin.login');
        }

        $phone = Session::get('admin_otp_phone');

        $result = $this->otpService->generateAndSendOtp($phone, 'admin');

        if ($result['success']) {
            return redirect()->route('admin.login')->with('otp_resent', true);
        }

        return redirect()->route('admin.login')->with('error', 'Failed to resend OTP.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::forget(['admin_otp_step', 'admin_otp_email', 'admin_otp_phone', 'admin_otp_user_id']);

        return redirect('/admin/login');
    }
}
