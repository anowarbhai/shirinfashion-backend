@extends('admin.layouts.master')

@section('title', 'Roles')

@section('header', 'Roles')

@section('content')
<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <h2 class="text-xl font-semibold">All Roles</h2>
        <a href="{{ route('admin.roles.create') }}" class="bg-rose-500 text-white px-4 py-2 rounded-lg hover:bg-rose-600 transition text-sm">
            <i class="fas fa-plus mr-1"></i>Add
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($roles as $role)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="font-medium">{{ $role->name }}</span>
                            @if($role->is_super)
                                <span class="ml-2 px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Super Admin</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $role->slug }}</td>
                    <td class="px-6 py-4">
                        @if($role->is_super)
                            <span class="text-sm text-purple-600 font-medium">All Permissions</span>
                        @else
                            <span class="text-sm text-gray-500">{{ $role->permissions->count() }} permissions</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500">{{ $role->users->count() }} users</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(!$role->is_super)
                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-sm" title="Super Admin role cannot be modified">
                                    <i class="fas fa-lock"></i>
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
        @foreach($roles as $role)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">{{ $role->name }}</h3>
                @if($role->is_super)
                <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full">Super Admin</span>
                @endif
            </div>

            <!-- Slug -->
            <p class="text-sm text-gray-500 mb-3 pb-3 border-b border-gray-100">{{ $role->slug }}</p>

            <!-- Details -->
            <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                @if($role->is_super)
                <span class="text-purple-600 font-medium">All Permissions</span>
                @else
                <span>{{ $role->permissions->count() }} permissions</span>
                @endif
                <span>{{ $role->users->count() }} users</span>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-800 p-2">
                    <i class="fas fa-eye"></i>
                </a>
                @if(!$role->is_super)
                <a href="{{ route('admin.roles.edit', $role) }}" class="text-yellow-600 hover:text-yellow-800 p-2">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @else
                <span class="text-gray-400 p-2" title="Super Admin role cannot be modified">
                    <i class="fas fa-lock"></i>
                </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>
</div>
@endsection
