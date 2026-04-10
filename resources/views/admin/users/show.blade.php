@extends('admin.layouts.master')

@section('title', $user->name)

@section('header', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                <div class="space-y-4">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Full Name</span>
                        <span class="font-medium">{{ $user->name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Email</span>
                        <span class="font-medium">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Phone</span>
                        <span class="font-medium">{{ $user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">NID</span>
                        <span class="font-medium">{{ $user->nid ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Date of Birth</span>
                        <span class="font-medium">{{ $user->date_of_birth ? $user->date_of_birth->format('d M Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Address</span>
                        <span class="font-medium">{{ $user->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Employment Information</h3>
                <div class="space-y-4">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Join Date</span>
                        <span class="font-medium">{{ $user->join_date ? $user->join_date->format('d M Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Status</span>
                        <span class="font-medium">
                            @if($user->status == 'active')
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactive</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Is Admin?</span>
                        <span class="font-medium">{{ $user->is_admin ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Member Since</span>
                        <span class="font-medium">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="text-center mb-4">
                    <div class="w-24 h-24 bg-rose-500 rounded-full flex items-center justify-center text-white text-4xl font-bold mx-auto mb-3">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h3 class="text-xl font-semibold">{{ $user->name }}</h3>
                    <p class="text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Assigned Roles</h3>
                @if($user->roles->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No roles assigned</p>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Orders</h3>
                <p class="text-3xl font-bold text-rose-500">{{ $user->orders->count() }}</p>
                <p class="text-gray-500 text-sm">Total Orders</p>
            </div>
        </div>
    </div>
</div>
@endsection
