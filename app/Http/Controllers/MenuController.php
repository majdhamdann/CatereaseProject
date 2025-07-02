<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{

    public function filterFoodItems(Request $request)
    {
        $serviceIds = $request->input('services', []);
        $categoryIds = $request->input('categories', []);
        $foodType = $request->input('type');

        $query = FoodItem::query();

        if (!empty($serviceIds)) {
            $query->whereHas('branch.branchServiceTypes', function ($q) use ($serviceIds) {
                $q->whereIn('service_type_id', $serviceIds)
                    ->where('is_available', true);
            });
        }

        if (!empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        if ($foodType) {
            $query->where('type', $foodType);
        }

        $foodItems = $query->with([
            'branch' => function($query) {
                $query->select('id', 'description', 'photo', 'location_note');
            },
            'category' => function($query) {
                $query->select('id', 'food_category_id')
                    ->with(['foodCategory' => function($q) {
                        $q->select('id', 'name');
                    }]);
            },
            'branch.branchServiceTypes' => function($query) use ($serviceIds) {
                $query->when(!empty($serviceIds), function($q) use ($serviceIds) {
                    $q->whereIn('service_type_id', $serviceIds);
                })
                    ->select('id', 'branch_id', 'service_type_id', 'custom_price')
                    ->with(['serviceType' => function($q) {
                        $q->select('id', 'name');
                    }]);
            }
        ])->get();

        $response = $foodItems->map(function($item) {
            $service = $item->branch->branchServiceTypes->first();
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => $item->price,
                'photo' => $item->photo,
                'calories' => $item->calories,
                'type' => $item->type,
                'branch' => [
                    'id' => $item->branch->id,
                    'description' => $item->branch->description,
                    'photo' => $item->branch->photo,
                    'location' => $item->branch->location_note,

                ],
                'category' => [
                    'id' => $item->category->id,
                    'name' => $item->category->foodCategory->name ?? null
                ],
                'service' => $service ? [
                    'id' => $service->serviceType->id,
                    'name' => $service->serviceType->name,
                    'price' => $service->custom_price
                ] : null
            ];

        });

        return response()->json($response);
    }
}
