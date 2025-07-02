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
            'branch',
            'category',
            'branch.branchServiceTypes.serviceType'
        ])->get();

        return response()->json($foodItems);
    }
}
