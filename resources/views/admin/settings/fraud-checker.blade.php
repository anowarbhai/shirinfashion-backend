@extends('admin.layouts.master')

@section('title', 'Fraud Checker - Shirin Fashion Admin')
@section('header', 'Fraud Checker Settings')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Settings Form -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">API Configuration</h3>
        
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.fraud-checker.update') }}">
            @csrf
            
            <div class="mb-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="fraud_checker_enabled" value="1" {{ $settings['fraud_checker_enabled'] ? 'checked' : '' }}
                        class="w-5 h-5 text-rose-600 rounded focus:ring-rose-500">
                    <span class="font-medium text-gray-700">Enable Fraud Checker</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">API URL</label>
                <input type="url" name="fraud_checker_api_url" value="{{ $settings['fraud_checker_api_url'] }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="https://fraudchecker.ocs-api.top/api/v3">
                @error('fraud_checker_api_url')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                <input type="text" name="fraud_checker_api_key" value="{{ $settings['fraud_checker_api_key'] }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500"
                    placeholder="Your API Key">
                @error('fraud_checker_api_key')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <h4 class="font-medium text-gray-700 mb-3 mt-6">Courier Services</h4>

            <div class="space-y-3 mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="fraud_checker_pathao" value="1" {{ $settings['fraud_checker_pathao'] ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 rounded focus:ring-rose-500">
                    <span class="text-gray-700">Pathao</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="fraud_checker_redx" value="1" {{ $settings['fraud_checker_redx'] ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 rounded focus:ring-rose-500">
                    <span class="text-gray-700">Redx</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="fraud_checker_carrybee" value="1" {{ $settings['fraud_checker_carrybee'] ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 rounded focus:ring-rose-500">
                    <span class="text-gray-700">Carrybee</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="fraud_checker_steadfast" value="1" {{ $settings['fraud_checker_steadfast'] ? 'checked' : '' }}
                        class="w-4 h-4 text-rose-600 rounded focus:ring-rose-500">
                    <span class="text-gray-700">Steadfast</span>
                </label>
            </div>

            <button type="submit" class="bg-rose-600 text-white px-6 py-2 rounded-lg hover:bg-rose-700">
                Save Settings
            </button>
        </form>
    </div>

    <!-- Test Section -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold text-gray-700 mb-4">Test Fraud Check</h3>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
            <div class="flex gap-2">
                <input type="text" id="testPhone" placeholder="01XXXXXXXXX"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-rose-500">
                <button type="button" onclick="checkFraud()" class="bg-rose-600 text-white px-4 py-2 rounded-lg hover:bg-rose-700">
                    Check
                </button>
            </div>
        </div>

        <div id="result" class="hidden mt-4">
            <div id="summaryResult" class="bg-white border rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <span id="statusBadge" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                        <span class="text-gray-600 font-medium">Score: <span id="scorePercent" class="font-bold text-lg"></span></span>
                    </div>
                    <div class="flex gap-6 text-center">
                        <div>
                            <div class="text-2xl font-bold" id="totalParcel">0</div>
                            <div class="text-xs text-gray-500">মোট</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600" id="successParcel">0</div>
                            <div class="text-xs text-gray-500">সফল</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600" id="cancelParcel">0</div>
                            <div class="text-xs text-gray-500">বাতিল</div>
                        </div>
                    </div>
                </div>
            </div>
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border px-3 py-2 text-left">কুরিয়ার</th>
                        <th class="border px-3 py-2 text-center">মোট</th>
                        <th class="border px-3 py-2 text-center">সফল</th>
                        <th class="border px-3 py-2 text-center">বাতিল</th>
                        <th class="border px-3 py-2 text-center">রেট</th>
                    </tr>
                </thead>
                <tbody id="courierTable">
                </tbody>
            </table>
            <details class="mt-4">
                <summary class="cursor-pointer text-sm text-gray-500 hover:text-gray-700">Raw JSON</summary>
                <pre id="resultContent" class="bg-gray-100 p-4 rounded-lg text-sm overflow-x-auto mt-2"></pre>
            </details>
        </div>
    </div>
</div>

<script>
const enabledCouriers = {
    pathao: {{ $settings['fraud_checker_pathao'] ? 'true' : 'false' }},
    steadfast: {{ $settings['fraud_checker_steadfast'] ? 'true' : 'false' }},
    redx: {{ $settings['fraud_checker_redx'] ? 'true' : 'false' }},
    carrybee: {{ $settings['fraud_checker_carrybee'] ? 'true' : 'false' }}
};

function checkFraud() {
    const phone = document.getElementById('testPhone').value;
    const resultDiv = document.getElementById('result');

    if (!phone) {
        alert('Please enter a phone number');
        return;
    }

    resultDiv.classList.remove('hidden');
    document.getElementById('resultContent').textContent = 'Loading...';

    fetch('{{ route('admin.settings.fraud-checker.test') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ phone: phone })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('resultContent').textContent = JSON.stringify(data, null, 2);
        
        if (data.error) {
            return;
        }
        
        const isSafe = data.status === 'Safe' || data.status === 'safe';
        const statusBadge = document.getElementById('statusBadge');
        if (isSafe) {
            statusBadge.textContent = '✅ নিরাপদ';
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
        } else {
            statusBadge.textContent = '⚠️ ' + (data.status || 'ঝুঁকিপূর্ণ');
            statusBadge.className = 'px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
        }
        
        document.getElementById('scorePercent').textContent = (data.score || 0) + '%';
        document.getElementById('totalParcel').textContent = data.total_parcel || 0;
        document.getElementById('successParcel').textContent = data.success_parcel || 0;
        document.getElementById('cancelParcel').textContent = data.cancel_parcel || 0;
        
        const response = data.response || {};
        const courierNames = {
            'pathao': 'Pathao',
            'steadfast': 'Steadfast',
            'redx': 'RedX',
            'carrybee': 'CarryBee'
        };
        
        let tableHTML = '';
        for (const [key, value] of Object.entries(response)) {
            if (!enabledCouriers[key]) continue;
            if (!value || !value.data) continue;
            const name = courierNames[key] || key;
            const d = value.data;
            const total = d.total || 0;
            const success = d.success || 0;
            const cancelled = d.cancel || 0;
            const rate = d.deliveredPercentage || 0;
            
            tableHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="border px-3 py-2 font-medium">${name}</td>
                    <td class="border px-3 py-2 text-center">${total}</td>
                    <td class="border px-3 py-2 text-center text-green-600">${success}</td>
                    <td class="border px-3 py-2 text-center text-red-600">${cancelled}</td>
                    <td class="border px-3 py-2 text-center">${rate}%</td>
                </tr>
            `;
        }
        
        document.getElementById('courierTable').innerHTML = tableHTML || '<tr><td colspan="5" class="border px-3 py-2 text-center text-gray-500">No courier data</td></tr>';
    })
    .catch(error => {
        document.getElementById('resultContent').textContent = 'Error: ' + error.message;
    });
}
</script>
@endsection
