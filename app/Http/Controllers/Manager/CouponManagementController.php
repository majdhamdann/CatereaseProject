<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponManagementController extends Controller
{
    public function createCoupon(Request $request)
{
    $manager = auth()->user();
    $validated = $request->validate([
        'branch_id'=>'required|exists:branches,id',
        'code' => 'required|string|unique:coupons,code',
        'discount_amount' => 'required|numeric|min:0',
        'expiration_date' => 'required|date|after:today',
        'package_ids' => 'required|array|min:1',
        'package_ids.*' => 'exists:packages,id',
    ]);

    $branchId = Branch::where('manager_id', $manager->id)->value('id');
    if (!$branchId) {
        return response()->json(['message' => 'لم يتم العثور على فرع مرتبط بهذا المدير'], 404);
    }

    $coupon = Coupon::create([
        'branch_id' => $branchId,
        'code' => $validated['code'],
        'discount_amount' => $validated['discount_amount'],
        'expiration_date' => $validated['expiration_date'],
    ]);
    $coupon->packages()->attach($validated['package_ids']);

    return response()->json([
        'message' => 'تم إنشاء الكوبون وربطه بالباكجات بنجاح',
        'coupon' => $coupon->load('packages') 
    ]);
}


}
