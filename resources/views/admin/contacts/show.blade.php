@extends('admin.layouts.master')

@section('title', 'View Message')

@section('header', 'Contact Message Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200 flex justify-between items-start">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Message from {{ $contact->name }}</h2>
            <p class="text-gray-500 mt-1">Received on {{ $contact->created_at->format('F d, Y at h:i A') }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if($contact->status !== 'replied')
            <form action="{{ route('admin.contacts.replied', $contact) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
                    <i class="fas fa-check mr-2"></i>Mark as Replied
                </button>
            </form>
            @endif
            <a href="{{ route('admin.contacts.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <p class="text-gray-900 bg-gray-50 px-4 py-3 rounded">{{ $contact->name }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <p class="bg-gray-50 px-4 py-3 rounded">
                    <a href="mailto:{{ $contact->email }}" class="text-rose-600 hover:underline">{{ $contact->email }}</a>
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <p class="text-gray-900 bg-gray-50 px-4 py-3 rounded">{{ $contact->phone ?: 'Not provided' }}</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <p class="bg-gray-50 px-4 py-3 rounded">
                    @if($contact->status === 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($contact->status === 'read')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Read</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Replied</span>
                    @endif
                </p>
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <div class="bg-gray-50 px-4 py-4 rounded text-gray-800 whitespace-pre-wrap">{{ $contact->message }}</div>
        </div>
        
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition">
                    <i class="fas fa-trash mr-2"></i>Delete Message
                </button>
            </form>
            
            <div class="text-sm text-gray-500">
                <p>Received: {{ $contact->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
