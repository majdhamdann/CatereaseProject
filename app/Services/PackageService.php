<?php


namespace App\Services;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;


class PackageService
{
    public function getBranchPackages($branchId)
    {
        DB::beginTransaction();

        $branch = Branch::with(['packages' => function ($q) {
            $q->where('is_active', true);
        }, 'restaurant'])->find($branchId);

        if (!$branch) {
            DB::rollBack();
            return [
                'status' => false,
                'code' => 404,
                'message' => 'Branch not found'
            ];
        }

        $packages = $branch->packages->map(function ($package) {
            return [
                'id' => $package->id,
                'name' => $package->name,
                'photo' => $package->photo,
                'description' => $package->description,
            ];
        });

        DB::commit();

        return [
            'status' => true,
            'code' => 200,
            'data' => [
                'branch_id' => $branch->id,
                'branch_name' => $branch->restaurant->name ?? 'Unknown',
                'packages' => $packages
            ]
        ];
    }


}
