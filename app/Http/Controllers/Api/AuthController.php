<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
        ]);

        // Link any guest orders with this phone number to the user
        $this->linkOrdersToUser($user);

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Registration successful', 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string',  // Can be email or phone
            'password' => 'required|string',
        ]);

        // Check if input is email or phone
        $input = $validated['email'];

        // Determine if it's an email or phone number
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            // It's an email
            $user = User::where('email', $input)->first();
        } else {
            // It's a phone number - normalize and search
            $normalizedPhone = preg_replace('/[^0-9]/', '', $input);
            // Also try without leading 0 or +88
            $user = User::whereRaw("REPLACE(REPLACE(REPLACE(phone, '+88', ''), '0', ''), ' ', '') LIKE ?", ['%'.$normalizedPhone])
                ->orWhere('phone', 'like', '%'.$input)
                ->first();
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        // Link any guest orders with this phone number to the user
        $this->linkOrdersToUser($user);

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Login with OTP (for OTP-based login)
     */
    public function otpLogin(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
        ]);

        // Verify OTP first
        $otpService = app(\App\Services\OtpService::class);

        if (! $otpService->verifyOtp($validated['phone'], $validated['otp'], 'customer')) {
            return $this->error('Invalid or expired OTP', 401);
        }

        // Find user by phone
        $normalizedPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        $user = User::whereRaw("REPLACE(REPLACE(REPLACE(phone, '+88', ''), '0', ''), ' ', '') LIKE ?", ['%'.$normalizedPhone])
            ->orWhere('phone', 'like', '%'.$validated['phone'])
            ->first();

        if (! $user) {
            return $this->error('No account found with this phone number', 404);
        }

        // Link any guest orders with this phone number to the user
        $this->linkOrdersToUser($user);

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Register new user with OTP verification
     */
    public function otpRegister(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:8',
        ]);

        // Convert empty password to null
        $validated['password'] = ! empty($validated['password'] ?? null) ? $validated['password'] : null;

        // Verify OTP first
        $otpService = app(\App\Services\OtpService::class);

        if (! $otpService->verifyOtp($validated['phone'], $validated['otp'], 'customer')) {
            return $this->error('Invalid or expired OTP', 401);
        }

        // Check if user already exists
        $normalizedPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        $existingUser = User::whereRaw("REPLACE(REPLACE(REPLACE(phone, '+88', ''), '0', ''), ' ', '') LIKE ?", ['%'.$normalizedPhone])
            ->orWhere('phone', 'like', '%'.$validated['phone'])
            ->first();

        if ($existingUser) {
            // Just login
            $this->linkOrdersToUser($existingUser);
            $token = $existingUser->createToken('auth-token')->plainTextToken;

            return $this->success([
                'user' => $existingUser,
                'token' => $token,
                'is_new' => false,
            ], 'Login successful');
        }

        // Create new user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'] ? Hash::make($validated['password']) : null,
            'phone' => $validated['phone'],
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'token' => $token,
            'is_new' => true,
        ], 'Registration successful', 201);
    }

    /**
     * Link guest orders (by phone) to the user
     */
    private function linkOrdersToUser(User $user)
    {
        if (! $user->phone) {
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $user->phone);

        // Find orders - exact match after normalization
        $orders = Order::whereNull('user_id')
            ->whereRaw("REPLACE(REPLACE(REPLACE(customer_phone, '+88', ''), ' ', ''), '-', '') = ?", [$phone])
            ->get();

        foreach ($orders as $order) {
            $order->update(['user_id' => $user->id, 'session_id' => null]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    public function me(Request $request)
    {
        return $this->success($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $user->update($validated);

        // If phone was updated, link any guest orders with the new phone
        if (isset($validated['phone'])) {
            $this->linkOrdersToUser($user);
        }

        return $this->success($user, 'Profile updated');
    }

    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old avatar if exists
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            unlink(public_path($user->avatar));
        }

        // Upload new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $avatarUrl = '/storage/'.$avatarPath;

        $user->update(['avatar' => $avatarUrl]);

        return $this->success($user, 'Avatar updated successfully');
    }

    public function linkOrders(Request $request)
    {
        $user = $request->user();

        if (! $user->phone) {
            return $this->error('No phone number on file', 400);
        }

        $phone = preg_replace('/[^0-9]/', '', $user->phone);

        // Exact match after normalization
        $linked = Order::where(function ($q) use ($phone) {
            $q->whereNull('user_id')
                ->whereRaw("REPLACE(REPLACE(REPLACE(customer_phone, '+88', ''), ' ', ''), '-', '') = ?", [$phone]);
        })
            ->update(['user_id' => $user->id, 'session_id' => null]);

        return $this->success(['linked' => $linked], "Linked $linked orders to your account");
    }

    public function setPassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->success($user, 'Password set successfully');
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Find user by phone
        $normalizedPhone = preg_replace('/[^0-9]/', '', $validated['phone']);
        $user = User::whereRaw("REPLACE(REPLACE(REPLACE(phone, '+88', ''), '0', ''), ' ', '') LIKE ?", ['%'.$normalizedPhone])
            ->orWhere('phone', 'like', '%'.$validated['phone'])
            ->first();

        if (! $user) {
            return $this->error('No account found with this phone number', 404);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->success(null, 'Password reset successfully');
    }
}
