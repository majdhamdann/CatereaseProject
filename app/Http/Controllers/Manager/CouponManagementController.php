<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Coupon;
use App\Models\Package;
use Illuminate\Http\Request;

class CouponManagementController extends Controller
{
    public function index()
{
    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id');

    $coupons = Coupon::with(['packages', 'user'])
        ->where('branch_id', $branchId)
        ->get();

    return response()->json($coupons);
}
   public function createCoupon(Request $request)
{
    $manager = auth()->user();

    $validated = $request->validate([
        'branch_id' => 'required|exists:branches,id',
        'code' => [
            'required',
            'string',
            'unique:coupons,code',
            'regex:/^[a-zA-Z0-9]{5,7}$/'
        ],
        'discount_amount' => 'required|numeric|min:0',
        'expiration_date' => 'required|date|after:today',
        'package_ids' => 'nullable|array|min:1',
        'package_ids.*' => 'exists:packages,id',
        'user_id' => 'nullable|exists:users,id', 
    ]);

    $branchId = Branch::where('manager_id', $manager->id)->value('id');
    if ($branchId != $validated['branch_id']) {
        return response()->json(['message' => 'لا يمكنك إنشاء كوبون لفرع لا تملكه.'], 403);
    }

    $coupon = Coupon::create([
        'branch_id' => $branchId,
        'user_id' => $validated['user_id'] ?? null, 
        'code' => $validated['code'],
        'discount_amount' => $validated['discount_amount'],
        'expiration_date' => $validated['expiration_date'],
    ]);

  //  $coupon->packages()->attach($validated['package_ids']);

    return response()->json([
        'message' => 'تم إنشاء الكوبون وربطه بالباكجات بنجاح',
        'coupon' => $coupon->load([ 'user']) 
    ]);
}

     public function update(Request $request, $id)
{
    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id');

    $coupon = Coupon::where('id', $id)->where('branch_id', $branchId)->firstOrFail();

    $validated = $request->validate([
        'code' => 'sometimes|required|string|unique:coupons,code,' . $coupon->id,
        'discount_amount' => 'sometimes|required|numeric|min:0',
        'expiration_date' => 'sometimes|required|date|after:today',
       // 'package_ids' => 'nullable|array',
        //'package_ids.*' => 'exists:packages,id',
        'user_id' => 'nullable|exists:users,id', 
    ]);

    $coupon->update($validated);

   
    return response()->json([
        'message' => 'تم التحديث',
        'coupon' => $coupon->load([ 'user']) 
    ]);
}


    public function destroy($id)
    {
        $manager = auth()->user();
        $branchId = Branch::where('manager_id', $manager->id)->value('id');

        $coupon = Coupon::where('id', $id)->where('branch_id', $branchId)->firstOrFail();

        $coupon->delete();

        return response()->json(['message' => 'تم حذف الكوبون بنجاح']);
    }

    public function packagesWithCoupons()
    {
        $manager = auth()->user();
        $branchId = Branch::where('manager_id', $manager->id)->value('id');

       
        $packages = Package::with('coupons')
            ->where('branch_id', $branchId)
            ->whereHas('coupons')
            ->get();


        return response()->json($packages);
    }



}
