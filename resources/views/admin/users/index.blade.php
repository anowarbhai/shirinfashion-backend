@extends('admin.layouts.master')

@section('title', 'Users')

@section('header', 'User Management')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search..." 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500 w-full md:w-40">
            <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $roleId == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-rose-500 text-white px-4 py-2 rounded-lg hover:bg-rose-600">
                Filter
            </button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="bg-rose-500 text-white px-4 py-2 rounded-lg hover:bg-rose-600 transition text-sm">
            <i class="fas fa-plus mr-1"></i>Add
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Join Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-rose-500 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $user->phone ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        @if($user->roles->count() > 0)
                            @foreach($user->roles->take(2) as $role)
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full mr-1">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                            @if($user->roles->count() > 2)
                                <span class="text-xs text-gray-500">+{{ $user->roles->count() - 2 }}</span>
                            @endif
                        @else
                            <span class="text-sm text-gray-500">No role</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $user->join_date ? $user->join_date->format('d M Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($user->status == 'active')
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
        @foreach($users as $user)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- User Info -->
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-rose-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Roles -->
            <div class="flex flex-wrap gap-1 mb-2">
                @if($user->roles->count() > 0)
                    @foreach($user->roles->take(2) as $role)
                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            {{ $role->name }}
                        </span>
                    @endforeach
                    @if($user->roles->count() > 2)
                        <span class="text-xs text-gray-500">+{{ $user->roles->count() - 2 }}</span>
                    @endif
                @else
                    <span class="text-xs text-gray-500">No role</span>
                @endif
            </div>

            <!-- Details -->
            <div class="flex items-center justify-between text-sm text-gray-600 mb-3 pb-3 border-b border-gray-100">
                <span>{{ $user->phone ?? 'N/A' }}</span>
                <span>{{ $user->join_date ? $user->join_date->format('d M Y') : 'N/A' }}</span>
            </div>

            <!-- Status & Actions -->
            <div class="flex items-center justify-between">
                <span class="px-2 py-1 text-xs font-medium rounded-full @if($user->status == 'active') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                    @if($user->status == 'active') Active @else Inactive @endif
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 p-2">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-800 p-2">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
