<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\FoodCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\BranchService;


class BranchController extends Controller
{
    protected $branchService;

    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }
    public function getAllBranchesWithDetails()
    {
        $results = $this->branchService->getAllBranchesWithDetails();
        return response()->json($results, 200);
    }

    public function getNearby(Request $request)
    {
        $branches = $this->branchService->getNearbyBranches($request);

        if ($branches === null) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must provide either the location (lat/lng) or the city ID (city_id)'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Nearby branches retrieved',
            'data' => $branches
        ]);
    }


//    public function getBranchesByCategoryName($categoryName)
//    {

//        $user = Auth::user();
//        if (!$user) {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Unauthenticated. Please log in.'
//            ], 401);
//        }

//        $foodCategory = FoodCategory::where('name', $categoryName)->first();
//
//        if (!$foodCategory) {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Category not found'
//            ], 404);
//        }
//

//        $user = Auth::user();
//        $defaultAddress = $user->addresses()->where('is_default', true)->first();
//
//        if (!$defaultAddress || !$defaultAddress->latitude || !$defaultAddress->longitude) {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'User location not available'
//            ], 400);
//        }
//

//        $branches = Branch::select('branches.*')
//            ->selectRaw(
//                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
//                [$defaultAddress->latitude, $defaultAddress->longitude, $defaultAddress->latitude]
//            )
//            ->whereHas('categories', function ($q) use ($foodCategory) {
//                $q->where('food_category_id', $foodCategory->id);
//            })
//            ->with(['restaurant:id,name', 'categories.foodCategory:id,name'])
//            ->orderBy('distance')
//            ->get();
//
//        return response()->json([
//            'status' => 'success',
//            'category' => $foodCategory->name,
//            'user_location' => [
//                'latitude' => $defaultAddress->latitude,
//                'longitude' => $defaultAddress->longitude
//            ],
//            'data' => $branches->map(function ($branch) {
//                return [
//                    'branch_id' => $branch->id,
//                    'restaurant' => $branch->restaurant->name ?? null,
//                    'description' => $branch->description,
//                    'location_note' => $branch->location_note,
//                    'latitude' => $branch->latitude,
//                    'longitude' => $branch->longitude,
//                    'distance_km' => round($branch->distance, 2),
//                    'categories' => $branch->categories->map(fn ($cat) => $cat->foodCategory->name)->unique()->values()
//                ];
//            }),
//        ]);
//    }

    public function getBranchesByCategoryName($categoryName)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated. Please log in.'
            ], 401);
        }

        $foodCategory = FoodCategory::where('name', $categoryName)->first();

        if (!$foodCategory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $defaultAddress = $user->addresses()->where('is_default', true)->first();


        if (!$defaultAddress || !$defaultAddress->latitude || !$defaultAddress->longitude) {
            $branches = Branch::whereHas('categories', function ($q) use ($foodCategory) {
                $q->where('food_category_id', $foodCategory->id);
            })
                ->with(['restaurant:id,name', 'categories.foodCategory:id,name'])
                ->get();

            return response()->json([
                'status' => 'success',
                'category' => $foodCategory->name,
                'user_location' => null,
                'data' => $branches->map(function ($branch) {
                    return [
                        'branch_id' => $branch->id,
                        'restaurant' => $branch->restaurant->name ?? null,
                        'description' => $branch->description,
                        'location_note' => $branch->location_note,
                        'latitude' => $branch->latitude,
                        'longitude' => $branch->longitude,
                        'distance_km' => null,
                        'categories' => $branch->categories->map(fn ($cat) => $cat->foodCategory->name)->unique()->values()
                    ];
                }),
            ]);
        }


        $branches = Branch::select('branches.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$defaultAddress->latitude, $defaultAddress->longitude, $defaultAddress->latitude]
            )
            ->whereHas('categories', function ($q) use ($foodCategory) {
                $q->where('food_category_id', $foodCategory->id);
            })
            ->with(['restaurant:id,name', 'categories.foodCategory:id,name'])
            ->orderBy('distance')
            ->get();

        return response()->json([
            'status' => 'success',
            'category' => $foodCategory->name,
            'user_location' => [
                'latitude' => $defaultAddress->latitude,
                'longitude' => $defaultAddress->longitude
            ],
            'data' => $branches->map(function ($branch) {
                return [
                    'branch_id' => $branch->id,
                    'restaurant' => $branch->restaurant->name ?? null,
                    'description' => $branch->description,
                    'location_note' => $branch->location_note,
                    'latitude' => $branch->latitude,
                    'longitude' => $branch->longitude,
                    'distance_km' => round($branch->distance, 2),
                    'categories' => $branch->categories->map(fn ($cat) => $cat->foodCategory->name)->unique()->values()
                ];
            }),
        ]);
    }


}
