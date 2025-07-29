<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Services\Manager\CouponService;

class CouponManagementController extends Controller
{
    protected $service;

    public function __construct(CouponService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $coupons = $this->service->getAll();
        return response()->json($coupons);
    }

    public function createCoupon(StoreCouponRequest $request)
    {
        $coupon = $this->service->create($request->validated());

        if (!$coupon) {
            return response()->json(['message' => 'لا يمكنك إنشاء كوبون لفرع لا تملكه.'], 403);
        }

        return response()->json([
            'message' => 'تم إنشاء الكوبون بنجاح',
            'coupon' => $coupon,
        ]);
    }

    public function update(UpdateCouponRequest $request, $id)
    {
        $coupon = $this->service->update($id, $request->validated());

        if (!$coupon) {
            return response()->json(['message' => 'لا يمكنك تعديل هذا الكوبون'], 403);
        }

        return response()->json([
            'message' => 'تم التحديث',
            'coupon' => $coupon,
        ]);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['message' => 'لا يمكنك حذف هذا الكوبون'], 403);
        }

        return response()->json(['message' => 'تم حذف الكوبون بنجاح']);
    }

    public function packagesWithCoupons()
    {
        $packages = $this->service->getPackagesWithCoupons();
        return response()->json($packages);
    }
}
