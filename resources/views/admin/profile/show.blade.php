@extends('admin.layouts.master')

@section('title', 'My Profile')

@section('header', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Profile Information</h2>
                <p class="text-gray-500 mt-1">Update your account's profile information</p>
            </div>
            
            <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Avatar -->
                <div class="flex flex-col items-center mb-6">
                    <div class="relative mb-3">
                        @if($user->avatar)
                        <img src="{{ asset($user->avatar) }}" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-rose-100">
                        @else
                        <div class="w-24 h-24 bg-rose-500 rounded-full flex items-center justify-center text-white text-4xl font-bold border-4 border-rose-100">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        @endif
                    </div>
                    <label for="avatar" class="cursor-pointer px-4 py-2 bg-rose-100 text-rose-700 rounded-lg hover:bg-rose-200 text-sm font-medium transition">
                        <i class="fas fa-camera mr-2"></i>Change Photo
                    </label>
                    <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG. Max 2MB</p>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        required>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        required>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="e.g., 017XXXXXXXX">
                    <p class="text-sm text-gray-500 mt-1">Required for OTP login</p>
                </div>

                <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg hover:bg-rose-600 transition">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Account Details & Password -->
        <div class="space-y-6">
            <!-- Role Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Account Details</h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Role</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-rose-100 text-rose-800">
                            <i class="fas fa-shield-alt mr-2"></i>Administrator
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Status</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-2"></i>Active
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Member Since</span>
                        <span class="text-gray-900 font-medium">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600">Last Updated</span>
                        <span class="text-gray-900 font-medium">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Change Password</h2>
                    <p class="text-gray-500 mt-1">Update your account password</p>
                </div>
                
                <form action="{{ route('admin.profile.password') }}" method="POST" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" name="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            required minlength="8">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                            required>
                    </div>

                    <button type="submit" class="w-full bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition">
                        Change Password
                    </button>
                </form>
            </div>
</div>
</div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = input.closest('.flex');
            let img = container.querySelector('img');
            if (!img) {
                const div = container.querySelector('.w-24.h-24');
                if (div) {
                    div.innerHTML = `<img src="${e.target.result}" alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-rose-100">`;
                    div.className = 'w-24 h-24 rounded-full object-cover border-4 border-rose-100';
                }
            } else {
                img.src = e.target.result;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
