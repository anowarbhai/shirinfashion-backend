<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    /**
     * Display SMS settings
     */
    public function index()
    {
        $settings = SmsSetting::getSettings();
        $smsService = new SmsService();
        $balance = $smsService->checkBalance();
        
        return view('admin.sms.index', compact('settings', 'balance'));
    }

    /**
     * Update SMS settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string|max:255',
            'sender_id' => 'required|string|max:20',
            'environment' => 'required|in:sandbox,live',
            'order_placement_template' => 'required|string|max:500',
            'order_status_template' => 'required|string|max:500',
            'otp_template' => 'required|string|max:500',
        ]);

        // Handle checkboxes (when unchecked, they don't send any value)
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['order_status_sms'] = $request->has('order_status_sms') ? 1 : 0;
        $validated['order_placement_sms'] = $request->has('order_placement_sms') ? 1 : 0;
        $validated['admin_login_otp'] = $request->has('admin_login_otp') ? 1 : 0;
        $validated['customer_login_otp'] = $request->has('customer_login_otp') ? 1 : 0;

        $settings = SmsSetting::getSettings();
        $settings->update($validated);

        return redirect()->route('admin.sms.index')->with('success', 'SMS settings updated successfully');
    }

    /**
     * Test SMS sending
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'test_number' => 'required|string|max:20',
            'test_message' => 'required|string|max:160',
        ]);

        $smsService = new SmsService();
        $success = $smsService->sendSms($validated['test_number'], $validated['test_message']);

        if ($success) {
            return redirect()->route('admin.sms.index')->with('success', 'Test SMS sent successfully');
        }

        return redirect()->route('admin.sms.index')->with('error', 'Failed to send test SMS. Please check your settings.');
    }

    /**
     * Check SMS balance
     */
    public function balance()
    {
        $smsService = new SmsService();
        $balance = $smsService->checkBalance();

        if ($balance) {
            return response()->json(['success' => true, 'balance' => $balance]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to check balance']);
    }
}
