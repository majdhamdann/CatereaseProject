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


//    public function getPackageById($id)
//    {
//        try {
//            DB::beginTransaction();
//
//            $package = Package::with([
//                'items.foodItem',
//                'extras.foodItem',
//                'extras.branchServiceType.serviceType',
//                'categories',
//                //'branchServiceType.serviceType',
//                'extraServices.serviceType',
//                'occasionTypes',
//                'branch.restaurant'
//            ])->find($id);
//
//            if (!$package) {
//                DB::rollBack();
//                return null;
//            }
//
//            DB::commit();
//
//            return [
//                'id' => $package->id,
//                'name' => $package->name,
//                'description' => $package->description,
//                'serves_count' => $package->serves_count,
//                'photo' => $package->photo,
//                'base_price' => $package->base_price,
//                'prepayment_required' => $package->prepayment_required,
//                'prepayment_amount' => $package->prepayment_amount,
//                'branch_id' => $package->branch->id ?? null,
//                'branch_name' => $package->branch->name ?? ($package->branch->restaurant->name ?? 'Unknown'),
//
////
////                'service_type' => [
////                    'id'=>$package->branchServiceType->serviceType->id ,
////                    'name' => $package->branchServiceType->serviceType->name ?? null,
////                    'service_cost' => $package->branchServiceType->service_cost ?? null,
////                ],
//
//                'service_type' => $package->extraServices->map(fn($s) => [
//                    'id'            => $s->id,
//                    'name'          => $s->serviceType->name ?? null,
//                    'custom_price'  => $s->custom_price,
//                    //'service_cost'  => $s->service_cost,
//                ])->values(),
//
////
//                'occasion_types'      => $package->occasionTypes->map(fn($o) => [
//                    'id'   => $o->id,
//                    'name' => $o->name,
//                ])->values(),
//                // 'occasion_type' => $package->occasionType->name ?? null,
//
//                'categories' => $package->categories->pluck('name'),
//                'max_extra_persons' => $package->max_extra_persons,
//                'price_per_extra_person' => $package->price_per_extra_person,
//
//                'items' => $package->items->map(function ($item) {
//                    return [
//                        'food_item_id' => $item->food_item_id,
//                        'food_item_name' => $item->foodItem->name ?? null,
//                        'quantity' => $item->quantity,
//                        'is_optional' => $item->is_optional,
//                    ];
//                }),
//                'extras' => $package->extras->map(function ($extra) {
//                    $extraName = $extra->name;
//
//                    if ($extra->type === 'food_item' && $extra->foodItem) {
//                        $extraName = $extra->foodItem->name;
//                    } elseif ($extra->type === 'service' && $extra->branchServiceType && $extra->branchServiceType->serviceType) {
//                        $extraName = $extra->branchServiceType->serviceType->name;
//                    }
//
//                    return [
//                        'id' => $extra->id,
//                        'type' => $extra->type,
//                        'name' => $extraName,
//                        'price' => $extra->price,
//                        'is_optional' => $extra->is_optional,
//                    ];
//                }),
//            ];
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//            throw $e;
//        }
//    }
    public function getPackageById($id)
    {
        try {
            DB::beginTransaction();

            $package = Package::with([
                'items.foodItem',
                'extras.foodItem',
                'extras.branchServiceType.serviceType',
                'categories',
                'extraServices.serviceType',
                'occasionTypes',
                'branch.restaurant',
                'discounts'
            ])->find($id);

            if (!$package) {
                DB::rollBack();
                return null;
            }


            $now = now();
            $currentDiscount = $package->discounts
                ->where('is_active', true)
                ->where('start_at', '<=', $now)
                ->where('end_at', '>=', $now)
                ->first();

            DB::commit();

            return [
                'id' => $package->id,
                'name' => $package->name,
                'description' => $package->description,
                'serves_count' => $package->serves_count,
                'photo' => $package->photo,


                'base_price' => $currentDiscount
                    ? $package->base_price . ' - ' . $currentDiscount->value . '%'
                    : $package->base_price,

                'prepayment_required' => $package->prepayment_required,
                'prepayment_amount' => $package->prepayment_amount,
                'branch_id' => $package->branch->id ?? null,
                'branch_name' => $package->branch->name ?? ($package->branch->restaurant->name ?? 'Unknown'),

                'service_type' => $package->extraServices->map(fn($s) => [
                    'id'            => $s->id,
                    'name'          => $s->serviceType->name ?? null,
                    'custom_price'  => $s->custom_price,
                ])->values(),

                'occasion_types' => $package->occasionTypes->map(fn($o) => [
                    'id'   => $o->id,
                    'name' => $o->name,
                ])->values(),

                'categories' => $package->categories->pluck('name'),
                'max_extra_persons' => $package->max_extra_persons,
                'price_per_extra_person' => $package->price_per_extra_person,

                'items' => $package->items->map(function ($item) {
                    return [
                        'food_item_id' => $item->food_item_id,
                        'food_item_name' => $item->foodItem->name ?? null,
                        'quantity' => $item->quantity,
                        'is_optional' => $item->is_optional,
                    ];
                }),

                'extras' => $package->extras->map(function ($extra) {
                    $extraName = $extra->name;

                    if ($extra->type === 'food_item' && $extra->foodItem) {
                        $extraName = $extra->foodItem->name;
                    } elseif ($extra->type === 'service' && $extra->branchServiceType && $extra->branchServiceType->serviceType) {
                        $extraName = $extra->branchServiceType->serviceType->name;
                    }

                    return [
                        'id' => $extra->id,
                        'type' => $extra->type,
                        'name' => $extraName,
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
