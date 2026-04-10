@extends('admin.layouts.master')

@section('title', $role->name)

@section('header', 'Role Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">{{ $role->name }}</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.roles.edit', $role) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Role Information</h3>
            <div class="space-y-4">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Name</span>
                    <span class="font-medium">{{ $role->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Slug</span>
                    <span class="font-medium">{{ $role->slug }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Description</span>
                    <span class="font-medium">{{ $role->description ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-600">Created</span>
                    <span class="font-medium">{{ $role->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">
                Permissions 
                @if($role->is_super)
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Super Admin</span>
                @else
                    ({{ $role->permissions->count() }})
                @endif
            </h3>
            @if($role->is_super)
                <div class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-lg">
                    <i class="fas fa-shield-alt text-purple-600 text-2xl mr-3"></i>
                    <div>
                        <p class="font-medium text-purple-800">Super Admin has all permissions</p>
                        <p class="text-sm text-purple-600">This role automatically has access to all features without needing to assign individual permissions.</p>
                    </div>
                </div>
            @elseif($role->permissions->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($role->permissions as $permission)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            {{ $permission->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No permissions assigned</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-2">
            <h3 class="text-lg font-semibold mb-4">Users with this Role ({{ $role->users->count() }})</h3>
            @if($role->users->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Name</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Email</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Phone</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($role->users as $user)
                        <tr>
                            <td class="px-4 py-2">{{ $user->name }}</td>
                            <td class="px-4 py-2">{{ $user->email }}</td>
                            <td class="px-4 py-2">{{ $user->phone ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No users with this role</p>
            @endif
        </div>
    </div>
</div>
@endsection
