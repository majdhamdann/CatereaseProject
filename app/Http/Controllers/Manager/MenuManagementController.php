<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuManagementController extends Controller
{
   public function indexForManager()
   {
       $branch = Branch::where('manager_id', Auth::id())->first();

    if (!$branch) {
       return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 404);
    }

    $categories = $branch->categories;
 
   return response()->json($categories);
   }
   public function allCategory()
{
       $category=Category::all();
        return response()->json($category);
   }
   public function getPackagesByCategory(Request $request)
{
    $manager = Auth::user();

    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بهذا المدير'], 404);
    }

    $categoryId = $request->category_id;

    $packages = Package::with('feedbacks')
        ->where('branch_id', $branch->id)
        ->whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        })
        ->get();

    $data = $packages->map(function ($package) {
        $averageRating = $package->feedbacks->avg('score') ?? 0;
        $reviewsCount = $package->feedbacks->count();

        return [
            'id' => $package->id,
            'name' => $package->name,
            'photo' => $package->photo,
            'price' => $package->base_price,
            'average_rating' => round($averageRating, 2),
            'reviews_count' => $reviewsCount,
        ];
    });

    return response()->json([
        'branch' => $branch->location_note ?? $branch->description,
        'category_id' => $categoryId,
        'packages' => $data,
    ]);
}




}
