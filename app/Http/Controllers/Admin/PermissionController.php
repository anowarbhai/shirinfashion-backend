<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->get('group');

        if ($group) {
            $permissions = Permission::where('group', $group)->paginate(50);
        } else {
            $permissions = Permission::orderBy('group')->orderBy('name')->paginate(50);
        }

        $groups = Permission::select('group')->distinct()->whereNotNull('group')->orderBy('group')->pluck('group');

        return view('admin.permissions.index', compact('permissions', 'groups', 'group'));
    }

    public function create()
    {
        $groups = Permission::groups();

        return view('admin.permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'group' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Permission::create($validated);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully');
    }

    public function edit(Permission $permission)
    {
        $groups = Permission::groups();

        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
            'slug' => 'required|string|max:255|unique:permissions,slug,'.$permission->id,
            'group' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully');
    }
}
