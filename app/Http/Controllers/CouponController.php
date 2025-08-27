<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function applyCoupon(Request $request, $billId)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $bill = Bill::with('order.orderDetails.package')->find($billId);
        if (!$bill) {
            return response()->json([
                'success' => false,
                'message' => 'Bill not found.'
            ], 404);
        }

        if ($bill->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot apply coupon on a paid bill.'
            ], 400);
        }

        $coupon = Coupon::where('code', $request->coupon_code)->first();
        if (!$coupon || $coupon->expiration_date < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ], 404);
        }

        if ($coupon->used) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has already been used.'
            ], 400);
        }

        $originalAmount = $bill->amount;

        $discountRate   = $coupon->discount_amount;
        $discountAmount = round($originalAmount * ($discountRate / 100), 2);

        $finalAmount = max(0, $originalAmount - $discountAmount);

        $bill->amount = $finalAmount;
        $bill->save();

        $coupon->used = true;
        $coupon->user_id = $bill->user_id;
        $coupon->save();

        $prepaymentAmount = null;
        if ($bill->order && $bill->order->orderDetails) {
            foreach ($bill->order->orderDetails as $detail) {
                $package = $detail->package;
                if ($package && $package->prepayment_required) {
                    $prepaymentAmount = round($finalAmount * ($package->prepayment_amount / 100), 2);
                    break;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully.',
            'data' => [
                'bill_id'         => $bill->id,
                'original_amount' => $originalAmount,
                'discount_rate'   => $discountRate . '%',
                'discount_amount' => $discountAmount,
                'final_amount'    => $finalAmount,
                'coupon_code'     => $coupon->code,
                'prepayment'      => $prepaymentAmount,
            ]
        ], 200);
    }


    public function getUserCoupons()
    {
        try {
            $userId = Auth::id();

            $coupons = Coupon::where('user_id', $userId)
                ->where('expiration_date', '>=', now())
                ->where('used', false)
                ->select('id', 'code', 'discount_amount as discount_percentage', 'expiration_date', 'used')
                ->get();

            if ($coupons->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No available coupons found.',
                    'data'    => [],
                ], 200);
            }

            $formatted = $coupons->map(function ($coupon) {
                return [
                    'id'                  => $coupon->id,
                    'code'                => $coupon->code,
                    'discount_percentage' => number_format($coupon->discount_percentage, 2) . '%',
                    'expiration_date'     => $coupon->expiration_date,
                    'used'                => (bool) $coupon->used,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Coupons retrieved successfully.',
                'data'    => $formatted,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve coupons.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }






}
