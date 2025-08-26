<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CouponController extends Controller
{
    public function applyCoupon(Request $request, $billId)
    {

        $request->validate([
            'coupon_code' => 'required|string'
        ]);


        $bill = Bill::find($billId);
        if (!$bill) {
            return response()->json([
                'success' => false,
                'message' => 'Bill not found.'
            ], Response::HTTP_NOT_FOUND);
        }


        if ($bill->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot apply coupon on a paid bill.'
            ], Response::HTTP_BAD_REQUEST);
        }


        $coupon = Coupon::where('code', $request->coupon_code)->first();
        if (!$coupon || $coupon->expiration_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ], Response::HTTP_NOT_FOUND);
        }


        if ($coupon->used) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has already been used.'
            ], Response::HTTP_BAD_REQUEST);
        }


        $originalAmount = $bill->amount;
        $discountAmount = $coupon->discount_amount;
        $finalAmount = max(0, $originalAmount - $discountAmount);


        $bill->amount = $finalAmount;
        $bill->save();


        $coupon->used = true;
        $coupon->user_id = $bill->user_id;
        $coupon->save();

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'data' => [
                'bill_id' => $bill->id,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'coupon_code' => $coupon->code,
            ]
        ], Response::HTTP_OK);
    }
}
