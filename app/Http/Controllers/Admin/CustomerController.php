<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search by name, email, phone, or address
        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
            
            // Also search by address in orders
            $query->orWhereHas('orders', function ($q) use ($search) {
                $q->where('shipping_address', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->with(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(1);
        }])->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.customers.index', compact('customers'));
    }
    
    public function show(User $customer)
    {
        $customer->load(['orders' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        return view('admin.customers.show', compact('customer'));
    }
}
