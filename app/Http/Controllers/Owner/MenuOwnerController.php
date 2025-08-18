<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Package;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuOwnerController extends Controller
{
   
public function getCategoriesToBranch()
{
    $owner = Auth::user();
    
    $branches = Branch::whereHas('restaurant', function($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
        ->with('categories')
        ->get();

    if ($branches->isEmpty()) {
        return response()->json([
            'message' => 'لا توجد فروع لهذا المالك'
        ], 404);
    }

    return response()->json([
        'branches' => $branches
    ]);
}
public function getPackagesByCategory($category_id)
{
    $owner = Auth::user();

    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json([
            'message' => 'المطعم غير موجود أو لا ينتمي لهذا المالك'
        ], 404);
    }

    $packages = Package::whereHas('categories', function($query) use ($category_id) {
            $query->where('categories.id', $category_id);
        })
        ->whereHas('branch', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })
        ->with(['branch', 'categories'])
        ->get();

    return response()->json([
        'packages' => $packages
    ]);
}
}