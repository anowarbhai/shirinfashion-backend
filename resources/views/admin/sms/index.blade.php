@extends('admin.layouts.master')

@section('title', 'SMS Integration')

@section('header', 'SMS Integration Settings')

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- SMS Settings Form -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">SMS Configuration</h2>
            <p class="text-gray-500 mt-1">Configure your OneCodeSoft SMS gateway settings</p>
        </div>
        
        <form action="{{ route('admin.sms.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- API Configuration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                    <input type="text" name="api_key" value="{{ old('api_key', $settings->api_key) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="Enter your API key">
                    @error('api_key')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sender ID</label>
                    <input type="text" name="sender_id" value="{{ old('sender_id', $settings->sender_id) }}" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="e.g., 8809617626047">
                    @error('sender_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Environment -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Environment</label>
                <select name="environment" class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    <option value="sandbox" {{ old('environment', $settings->environment) === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                    <option value="live" {{ old('environment', $settings->environment) === 'live' ? 'selected' : '' }}>Live (Production)</option>
                </select>
            </div>

            <!-- Main Toggle -->
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                    {{ old('is_active', $settings->is_active) ? 'checked' : '' }}
                    class="w-5 h-5 text-rose-500 rounded focus:ring-rose-500">
                <label for="is_active" class="font-medium text-gray-700">Enable SMS Service</label>
            </div>

            <!-- Feature Toggles -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Feature Toggles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="order_status_sms" id="order_status_sms" value="1" 
                            {{ old('order_status_sms', $settings->order_status_sms) ? 'checked' : '' }}
                            class="w-5 h-5 text-rose-500 rounded focus:ring-rose-500">
                        <div>
                            <label for="order_status_sms" class="font-medium text-gray-700 block">Order Status SMS</label>
                            <span class="text-sm text-gray-500">Send SMS when order status changes</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="order_placement_sms" id="order_placement_sms" value="1" 
                            {{ old('order_placement_sms', $settings->order_placement_sms) ? 'checked' : '' }}
                            class="w-5 h-5 text-rose-500 rounded focus:ring-rose-500">
                        <div>
                            <label for="order_placement_sms" class="font-medium text-gray-700 block">Order Placement SMS</label>
                            <span class="text-sm text-gray-500">Send SMS on new order</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="admin_login_otp" id="admin_login_otp" value="1" 
                            {{ old('admin_login_otp', $settings->admin_login_otp) ? 'checked' : '' }}
                            class="w-5 h-5 text-rose-500 rounded focus:ring-rose-500">
                        <div>
                            <label for="admin_login_otp" class="font-medium text-gray-700 block">Admin Login OTP</label>
                            <span class="text-sm text-gray-500">Send OTP for admin login</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                        <input type="checkbox" name="customer_login_otp" id="customer_login_otp" value="1" 
                            {{ old('customer_login_otp', $settings->customer_login_otp) ? 'checked' : '' }}
                            class="w-5 h-5 text-rose-500 rounded focus:ring-rose-500">
                        <div>
                            <label for="customer_login_otp" class="font-medium text-gray-700 block">Customer Login OTP</label>
                            <span class="text-sm text-gray-500">Send OTP for customer login</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMS Templates -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">SMS Templates</h3>
                <p class="text-sm text-gray-500 mb-4">Use variables: {customer_name}, {order_number}, {total}, {status}, {otp}</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Placement Template</label>
                        <textarea name="order_placement_template" rows="2" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">{{ old('order_placement_template', $settings->order_placement_template) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Status Template</label>
                        <textarea name="order_status_template" rows="2" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">{{ old('order_status_template', $settings->order_status_template) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">OTP Template</label>
                        <textarea name="otp_template" rows="2" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500">{{ old('otp_template', $settings->otp_template) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 border-t pt-6">
                <button type="submit" class="bg-rose-500 text-white px-6 py-2 rounded-lg hover:bg-rose-600 transition">
                    Save Settings
                </button>
                @if($balance)
                    <div class="flex items-center gap-2 bg-green-100 text-green-800 px-4 py-2 rounded-lg">
                        <i class="fas fa-wallet"></i>
                        <span>Balance: {{ $balance['balance'] ?? 'N/A' }} SMS</span>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Test SMS Section -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Test SMS</h2>
            <p class="text-gray-500 mt-1">Send a test SMS to verify your configuration</p>
        </div>
        
        <form action="{{ route('admin.sms.test') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Number</label>
                    <input type="text" name="test_number" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="e.g., 017XXXXXXXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Test Message</label>
                    <input type="text" name="test_message" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="Enter test message">
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                <i class="fas fa-paper-plane mr-2"></i>Send Test SMS
            </button>
        </form>
    </div>

    <!-- API Documentation -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">API Documentation</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Live Endpoint</h4>
                    <code class="block bg-gray-100 p-3 rounded text-sm">https://sms.onecodesoft.com/api/</code>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Sandbox Endpoint</h4>
                    <code class="block bg-gray-100 p-3 rounded text-sm">https://sms.onecodesoft.com/api/sandbox/</code>
                </div>
            </div>
            <div class="mt-4">
                <h4 class="font-medium text-gray-700 mb-2">Quick Send URL (GET)</h4>
                <code class="block bg-gray-100 p-3 rounded text-sm break-all">
                    https://sms.onecodesoft.com/api/send-sms?api_key=YOUR_API_KEY&type=text&number=88017XXXXXXXX&senderid=YOUR_SENDER_ID&message=TestSMS
                </code>
            </div>
            <div class="mt-4">
                <h4 class="font-medium text-gray-700 mb-2">Response Codes</h4>
                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                    <li><strong>202</strong> - Success</li>
                    <li><strong>1007</strong> - Low Balance</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
