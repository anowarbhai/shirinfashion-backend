<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends BaseController
{
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return $this->error('Invalid coupon code', 404);
        }

        if (!$coupon->isValid()) {
            return $this->error('This coupon is not valid or has expired', 400);
        }

        $orderAmount = $request->order_amount;
        $discount = (float) $coupon->calculateDiscount($orderAmount);

        return $this->success([
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'description' => $coupon->description,
                'discount_type' => $coupon->discount_type,
                'discount_value' => (float) $coupon->discount_value,
                'min_order_amount' => $coupon->min_order_amount ? (float) $coupon->min_order_amount : null,
                'max_discount_amount' => $coupon->max_discount_amount ? (float) $coupon->max_discount_amount : null,
            ],
            'discount_amount' => $discount,
            'final_amount' => (float) ($orderAmount - $discount),
        ]);
    }
}
