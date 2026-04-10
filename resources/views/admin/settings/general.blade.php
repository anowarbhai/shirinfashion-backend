@extends('admin.layouts.master')

@section('title', 'General Settings - Shirin Fashion Admin')
@section('header', 'General Settings')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.general.update') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Site Info -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Site Information</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $generalSettings->site_name) }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                </div>
            </div>

            <!-- Currency Settings -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Currency Settings</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $generalSettings->currency_symbol) }}" required maxlength="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="৳">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency Code</label>
                    <input type="text" name="currency_code" value="{{ old('currency_code', $generalSettings->currency_code) }}" required maxlength="10"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                        placeholder="BDT">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency Position</label>
                    <select name="currency_position" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                        <option value="left" {{ old('currency_position', $generalSettings->currency_position) == 'left' ? 'selected' : '' }}>Left (৳100)</option>
                        <option value="right" {{ old('currency_position', $generalSettings->currency_position) == 'right' ? 'selected' : '' }}>Right (100৳)</option>
                    </select>
                </div>
            </div>

            <!-- Localization Settings -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Localization</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                    <select name="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                        <option value="Asia/Dhaka" {{ old('timezone', $generalSettings->timezone) == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (GMT+6)</option>
                        <option value="UTC" {{ old('timezone', $generalSettings->timezone) == 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                        <option value="Asia/Kolkata" {{ old('timezone', $generalSettings->timezone) == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (GMT+5:30)</option>
                        <option value="Asia/Kuala_Lumpur" {{ old('timezone', $generalSettings->timezone) == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Asia/Kuala_Lumpur (GMT+8)</option>
                        <option value="Asia/Singapore" {{ old('timezone', $generalSettings->timezone) == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (GMT+8)</option>
                        <option value="Asia/Tokyo" {{ old('timezone', $generalSettings->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (GMT+9)</option>
                        <option value="Europe/London" {{ old('timezone', $generalSettings->timezone) == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT+0)</option>
                        <option value="America/New_York" {{ old('timezone', $generalSettings->timezone) == 'America/New_York' ? 'selected' : '' }}>America/New_York (GMT-5)</option>
                    </select>
                </div>
            </div>

            <!-- Date & Time Settings -->
            <div class="space-y-4">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Date & Time Format</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Format</label>
                    <select name="date_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                        <option value="M d, Y" {{ old('date_format', $generalSettings->date_format) == 'M d, Y' ? 'selected' : '' }}>Apr 05, 2026</option>
                        <option value="d M Y" {{ old('date_format', $generalSettings->date_format) == 'd M Y' ? 'selected' : '' }}>05 Apr 2026</option>
                        <option value="d-m-Y" {{ old('date_format', $generalSettings->date_format) == 'd-m-Y' ? 'selected' : '' }}>05-04-2026</option>
                        <option value="Y-m-d" {{ old('date_format', $generalSettings->date_format) == 'Y-m-d' ? 'selected' : '' }}>2026-04-05</option>
                        <option value="F d, Y" {{ old('date_format', $generalSettings->date_format) == 'F d, Y' ? 'selected' : '' }}>April 05, 2026</option>
                        <option value="d F Y" {{ old('date_format', $generalSettings->date_format) == 'd F Y' ? 'selected' : '' }}>05 April 2026</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Format</label>
                    <select name="time_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                        <option value="h:i A" {{ old('time_format', $generalSettings->time_format) == 'h:i A' ? 'selected' : '' }}>12 Hour (03:45 PM)</option>
                        <option value="H:i" {{ old('time_format', $generalSettings->time_format) == 'H:i' ? 'selected' : '' }}>24 Hour (15:45)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Preview -->
        <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-700 mb-3">Preview</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Currency:</span>
                    <span class="font-medium ml-2">
                        @if($generalSettings->currency_position == 'left')
                            {{ $generalSettings->currency_symbol }}100.00
                        @else
                            100.00{{ $generalSettings->currency_symbol }}
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-gray-500">Date:</span>
                    <span class="font-medium ml-2">{{ \Carbon\Carbon::now($generalSettings->timezone)->format($generalSettings->date_format) }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Time:</span>
                    <span class="font-medium ml-2">{{ \Carbon\Carbon::now($generalSettings->timezone)->format($generalSettings->time_format) }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
