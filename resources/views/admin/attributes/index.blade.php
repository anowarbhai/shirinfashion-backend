@extends('admin.layouts.master')

@section('title', 'Attributes - Shirin Fashion Admin')
@section('header', 'Attributes')

@section('content')
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center flex-wrap gap-3">
        <form method="GET" class="relative flex-1 md:flex-none">
            <input type="text" name="search" placeholder="Search attributes..." value="{{ request('search') }}" class="w-full md:w-64 px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
            <button type="submit" class="absolute right-0 top-0 h-full bg-rose-600 text-white px-4 rounded-r-lg hover:bg-rose-700 transition"><i class="fas fa-search"></i></button>
        </form>
        <a href="{{ route('admin.attributes.create') }}" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700 transition font-medium flex items-center gap-2"><i class="fas fa-plus"></i>Add</a>
    </div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Values</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attributes as $attribute)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $attribute->name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $attribute->slug }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $attribute->values->pluck('value')->implode(', ') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.attributes.edit', $attribute) }}" class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No attributes found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($attributes as $attribute)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-gray-800">{{ $attribute->name }}</h3>
            </div>
            <p class="text-xs text-gray-500 mb-2">{{ $attribute->slug }}</p>
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach($attribute->values as $value)
                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $value->value }}</span>
                @endforeach
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <a href="{{ route('admin.attributes.edit', $attribute) }}" class="inline-flex items-center gap-1 text-yellow-600 hover:text-yellow-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-yellow-50"><i class="fas fa-edit"></i>Edit</a>
                <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">No attributes found</div>
        @endforelse
    </div>
    
    @if($attributes->hasPages())
    <div class="p-6 border-t border-gray-100">{{ $attributes->links() }}</div>
    @endif
</div>
@endsection
