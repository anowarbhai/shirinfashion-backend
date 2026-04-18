@extends('admin.layouts.master')

@section('title', 'Create Permission')

@section('header', 'Create Permission')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.permissions.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Permission Name</label>
                <input type="text" name="name" value="{{ old('name') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                    required placeholder="e.g., Manage Products">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                    required placeholder="e.g., products.view">
                <p class="text-xs text-gray-500 mt-1">Use dots: e.g., products.view, orders.edit</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Group</label>
                <select name="group" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    <option value="">Select Group</option>
                    @foreach($groups as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                    placeholder="Describe what this permission allows">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg hover:bg-rose-600 transition">
                    Create Permission
                </button>
                <a href="{{ route('admin.permissions.index') }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
