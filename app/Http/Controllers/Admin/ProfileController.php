<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Show admin profile
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('roles');

        return view('admin.profile.show', compact('user'));
    }

    /**
     * Update admin profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:users,phone,'.$user->id,
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['phone'])) {
            $updateData['phone'] = $validated['phone'];
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                $oldPath = str_replace('storage/', '', $user->avatar);
                \Storage::disk('public')->delete($oldPath);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = 'storage/'.$avatarPath;
        }

        $user->update($updateData);

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect',
            ]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.profile.show')->with('success', 'Password changed successfully');
    }
}
