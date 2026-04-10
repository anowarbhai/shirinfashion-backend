<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $roleId = $request->get('role');
        
        $query = User::query();
        
        // Only show staff users (is_admin = 1 OR has roles), exclude customers
        $query->where(function($q) {
            $q->where('is_admin', 1)
              ->orWhereHas('roles');
        });
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($roleId) {
            $query->whereHas('roles', function($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles', 'search', 'roleId'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'nid' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'join_date' => 'nullable|date',
            'address' => 'nullable|string',
            'is_admin' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        if (!isset($validated['is_admin'])) {
            $validated['is_admin'] = 0;
        }
        
        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }

        try {
            $user = User::create($validated);

            if ($request->filled('roles')) {
                $user->roles()->sync($request->roles);
            }

            return redirect()->route('admin.users.index')->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user->load('roles', 'orders');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'nid' => 'nullable|string|max:30',
            'date_of_birth' => 'nullable|date',
            'join_date' => 'nullable|date',
            'address' => 'nullable|string',
            'is_admin' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (!isset($validated['is_admin'])) {
            $validated['is_admin'] = 0;
        }
        
        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }

        $user->update($validated);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
