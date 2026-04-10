@extends('admin.layouts.master')

@section('title', 'Create Role')

@section('header', 'Create Role')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role Name</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_super" id="is_super" value="1" {{ old('is_super') ? 'checked' : '' }}
                    class="rounded text-purple-600 focus:ring-purple-500">
                <label for="is_super" class="ml-2 text-sm font-medium text-gray-700">
                    Super Admin Role
                    <span class="text-gray-500 text-xs block">Super Admin has all permissions and cannot be modified</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                @php
                    $groupedPermissions = $permissions->groupBy('group');
                @endphp
                <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-lg p-4 space-y-4">
                    @foreach($groupedPermissions as $group => $perms)
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">{{ $group }}</h4>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($perms as $permission)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                    class="rounded text-rose-500 focus:ring-rose-500">
                                <span class="text-sm text-gray-600">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg hover:bg-rose-600 transition">
                    Create Role
                </button>
                <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
