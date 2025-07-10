<?php


namespace App\Services;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use App\Models\Package;

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
                'serves_count' => $package->serves_count,
                'base_price' => $package->base_price,
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

    public function getPackageById($id)
    {
        try {
            DB::beginTransaction();

            $package = Package::with([
                'items.foodItem',
                'extras',
                'categories',
                'serviceType',
                'occasionType',
                'branch.restaurant'
            ])->find($id);

            if (!$package) {
                DB::rollBack();
                return null;
            }

            DB::commit();

            return [
                'id' => $package->id,
                'name' => $package->name,
                'description' => $package->description,
                'serves_count' => $package->serves_count,
                'photo' => $package->photo,
                'base_price' => $package->base_price,
                'prepayment_required' => $package->prepayment_required,
                'prepayment_amount' => $package->prepayment_amount,
                'branch_id' => $package->branch->id ?? null,
                'branch_name' => $package->branch->name ?? ($package->branch->restaurant->name ?? 'Unknown'),
                'service_type' => $package->serviceType->name ?? null,
                'occasion_type' => $package->occasionType->name ?? null,
                'categories' => $package->categories->pluck('name'),
                'items' => $package->items->map(function ($item) {
                    return [
                        'food_item_id' => $item->food_item_id,
                        'food_item_name' => $item->foodItem->name ?? null,
                        'quantity' => $item->quantity,
                        'is_optional' => $item->is_optional,
                    ];
                }),
                'extras' => $package->extras->map(function ($extra) {
                    return [
                        'name' => $extra->name,
                        'price' => $extra->price,
                        'is_optional' => $extra->is_optional,
                    ];
                }),
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function getAllActivePackages()
    {
        DB::beginTransaction();

        try {
            $packages = Package::select('id', 'name', 'photo', 'description','serves_count','base_price')
                ->where('is_active', true)
                ->get();

            DB::commit();

            return $packages;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




}
