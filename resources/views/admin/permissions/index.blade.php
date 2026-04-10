@extends('admin.layouts.master')

@section('title', 'Permissions')

@section('header', 'Permissions')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-semibold">All Permissions</h2>
            @if($groups->count() > 0)
            <select onchange="window.location.href = '{{ route('admin.permissions.index') }}?group=' + this.value" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">All Groups</option>
                @foreach($groups as $g)
                    <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>{{ $g }}</option>
                @endforeach
            </select>
            @endif
        </div>
        <a href="{{ route('admin.permissions.create') }}" class="bg-rose-500 text-white px-4 py-2 rounded-lg hover:bg-rose-600 transition text-sm">
            <i class="fas fa-plus mr-1"></i>Add
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($permissions as $permission)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $permission->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">{{ $permission->group }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $permission->slug }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
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
        @foreach($permissions as $permission)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900">{{ $permission->name }}</h3>
                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">{{ $permission->group }}</span>
            </div>

            <!-- Slug -->
            <p class="text-sm text-gray-500 mb-3 pb-3 border-b border-gray-100">{{ $permission->slug }}</p>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-yellow-600 hover:text-yellow-800 p-2">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $permissions->links() }}
    </div>
</div>
@endsection
