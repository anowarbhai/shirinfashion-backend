@extends('admin.layouts.master')

@section('title', 'Edit Attribute - Shirin Fashion Admin')
@section('header', 'Edit Attribute')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.attributes.update', $attribute) }}">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Name *</label><input type="text" name="name" value="{{ old('name', $attribute->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Slug *</label><input type="text" name="slug" value="{{ old('slug', $attribute->slug) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Values (one per line)</label>
                <textarea name="values_text" rows="4" placeholder="Red&#10;Blue&#10;Green" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">{{ old('values_text', $attribute->values->pluck('value')->implode("\n")) }}</textarea>
            </div>
        </div>
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.attributes.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700">Update Attribute</button>
        </div>
    </form>
</div>
@endsection
