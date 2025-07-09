<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchPackageController extends Controller
{
    public function index($branchId)
    {
        $branch = Branch::with(['packages' => function ($q) {
            $q->with([
                'items.foodItem',
                'extras',
                'categories',
                'serviceType',
                'occasionType'
            ])->where('is_active', true);
        }, 'restaurant'])->find($branchId);

        if (!$branch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Branch not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'branch_id' => $branch->id,
            'branch_name' => $branch->restaurant->name ?? 'Unknown',
            'packages' => $branch->packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'description' => $package->description,
                    'photo' => $package->photo,
                    'base_price' => $package->base_price,
                    'prepayment_required' => $package->prepayment_required,
                    'prepayment_amount' => $package->prepayment_amount,
                    'service_type' => $package->serviceType->name ?? null,
                    'occasion_type' => $package->occasionType->name ?? null,
                    'categories' => $package->categories->pluck('name'),
                    'items' => $package->items->map(fn($item) => [
                        'food_item_id' => $item->food_item_id,
                        'food_item_name' => $item->foodItem->name ?? null,
                        'quantity' => $item->quantity,
                        'is_optional' => $item->is_optional,
                    ]),
                    'extras' => $package->extras->map(fn($extra) => [
                        'name' => $extra->name,
                        'price' => $extra->price,
                        'is_optional' => $extra->is_optional,
                    ]),
                ];
            })
        ]);
    }
}
