<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Shirin Fashion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-serif font-bold text-rose-600">SHIRIN</h1>
                <p class="text-gray-500 text-sm">Admin Login</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('otp_sent'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm"><i class="fas fa-check-circle mr-2"></i>OTP sent to your phone number.</p>
                </div>
            @endif

            @if(session('otp_resent'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                    <p class="text-sm"><i class="fas fa-check-circle mr-2"></i>OTP resent successfully.</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($otpStep)
                {{-- OTP Verification Form --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-2xl text-rose-600"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-800">Enter OTP</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        We've sent a 6-digit OTP to<br>
                        <span class="font-medium text-gray-700">{{ $phone }}</span>
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            OTP Code
                        </label>
                        <input 
                            type="text" 
                            name="otp" 
                            required
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-rose-500 text-center text-2xl tracking-widest"
                            placeholder="000000"
                            autofocus
                        >
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-rose-600 text-white font-bold py-2 px-4 rounded hover:bg-rose-700 transition-colors"
                    >
                        Verify OTP
                    </button>
                </form>

                <div class="mt-6 text-center space-y-3">
                    <form method="POST" action="{{ route('admin.otp.resend') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-rose-600 hover:text-rose-700 font-medium">
                            <i class="fas fa-redo mr-1"></i>Resend OTP
                        </button>
                    </form>
                    <br>
                    <a href="{{ route('admin.login') }}" class="text-sm text-gray-500 hover:text-gray-700">
                        Back to Login
                    </a>
                </div>
            @else
                {{-- Email/Password Login Form --}}
                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Email Address
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-rose-500"
                            placeholder="admin@shirinfashion.com"
                            value="{{ old('email') }}"
                        >
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Password
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-rose-500"
                            placeholder="Enter your password"
                        >
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-rose-600 text-white font-bold py-2 px-4 rounded hover:bg-rose-700 transition-colors"
                    >
                        Login
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="/" class="text-sm text-gray-500 hover:text-rose-600">
                        Back to Home
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
