<?php
namespace App\Services\Manager;

use App\Models\Branch;
use App\Models\Coupon;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    public function getAll()
    {
        $branchId = $this->getManagerBranchId();
        return Coupon::with(['packages', 'user'])->where('branch_id', $branchId)->get();
    }

    public function create(array $data)
    {
        $branchId = $this->getManagerBranchId();

        if ($branchId != $data['branch_id']) {
            return null;
        }

        $coupon = Coupon::create([
            'branch_id' => $branchId,
            'user_id' => $data['user_id'] ?? null,
            'code' => $data['code'],
            'discount_amount' => $data['discount_amount'],
            'expiration_date' => $data['expiration_date'],
        ]);

        return $coupon->load('user');
    }

    public function update($id, array $data)
    {
        $coupon = $this->getCouponByManager($id);

        if (!$coupon) return null;

        $coupon->update($data);

        if (isset($data['package_ids'])) {
            $coupon->packages()->sync($data['package_ids']);
        }

        return $coupon->load('user');
    }

    public function delete($id)
    {
        $coupon = $this->getCouponByManager($id);

        if (!$coupon) return false;

        $coupon->delete();
        return true;
    }

    public function getPackagesWithCoupons()
    {
        $branchId = $this->getManagerBranchId();

        return Package::with('coupons')
            ->where('branch_id', $branchId)
            ->whereHas('coupons')
            ->get();
    }

    private function getCouponByManager($id)
    {
        $branchId = $this->getManagerBranchId();

        return Coupon::where('id', $id)
            ->where('branch_id', $branchId)
            ->first();
    }

    private function getManagerBranchId()
    {
        $manager = Auth::user();
        return Branch::where('manager_id', $manager->id)->value('id');
    }
}
