@extends('admin.layouts.master')

@section('title', 'Contact Messages')

@section('header', 'Contact Messages')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">All Messages</h2>
    </div>
    
    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($messages as $message)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-medium text-gray-900">{{ $message->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        <a href="mailto:{{ $message->email }}" class="text-rose-600 hover:underline">{{ $message->email }}</a>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $message->phone ?: 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600 max-w-xs">
                        <p class="truncate">{{ Str::limit($message->message, 50) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($message->status === 'pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($message->status === 'read')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Read</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Replied</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $message->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.contacts.show', $message) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.contacts.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        No contact messages found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3 p-4">
        @forelse($messages as $message)
        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $message->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $message->email }}</p>
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full @if($message->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($message->status === 'read') bg-blue-100 text-blue-800 @else bg-green-100 text-green-800 @endif">
                    @if($message->status === 'pending') Pending @elseif($message->status === 'read') Read @else Replied @endif
                </span>
            </div>

            <!-- Phone -->
            @if($message->phone)
            <p class="text-sm text-gray-600 mb-2">{{ $message->phone }}</p>
            @endif

            <!-- Message -->
            <p class="text-sm text-gray-600 mb-3 pb-3 border-b border-gray-100 line-clamp-3">{{ Str::limit($message->message, 100) }}</p>

            <!-- Footer -->
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">{{ $message->created_at->format('M d, Y H:i') }}</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.contacts.show', $message) }}" class="text-blue-600 hover:text-blue-800 p-2">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form action="{{ route('admin.contacts.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            No contact messages found.
        </div>
        @endforelse
    </div>
    
    <div class="p-4 border-t border-gray-200">
        {{ $messages->links() }}
    </div>
</div>
@endsection
