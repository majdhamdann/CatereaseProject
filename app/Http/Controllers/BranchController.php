<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\FoodCategory;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\BranchService;
use Illuminate\Support\Facades\Log;


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
    public function getItems($branchId)
    {
        $response = $this->branchService->getBranchItems($branchId);

        return response()->json([
            'status' => $response['status'],
            'message' => $response['message'] ?? 'Items fetched successfully.',
            'data' => $response['data'] ?? null,
        ], $response['code']);
    }

    public function getBranchesByCategoryName($categoryName)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated. Please log in.'
            ], 401);
        }


        $category = Category::where('name', $categoryName)->first();
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }


        $lastAddress = $user->addresses()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest()
            ->first();

        if (!$lastAddress) {
            $branches = $this->getBranchesWithoutLocation($category);
            return response()->json([
                'status' => 'success',
                'message' => 'Showing all branches as no valid location found',
                'category' => $category->name,
                'data' => $branches
            ]);
        }


        $branches = Branch::select('branches.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$lastAddress->latitude, $lastAddress->longitude, $lastAddress->latitude]
            )
            ->whereHas('foodCategories.category', function($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->with(['restaurant:id,name', 'foodCategories' => function($query) {
                $query->with('category:id,name');
            }])
            ->orderBy('distance')
            ->get();

        return response()->json([
            'status' => 'success',
            'category' => $category->name,
            'location_source' => 'last_used_address',
            'user_location' => [
                'latitude' => $lastAddress->latitude,
                'longitude' => $lastAddress->longitude,
                'address_id' => $lastAddress->id
            ],
            'data' => $branches->map(function ($branch) {
                return [
                    'branch_id' => $branch->id,
                    'restaurant' => $branch->restaurant->name ?? null,
                    'photo' => $branch->photo,
                    'description' => $branch->description,
                    'location_note' => $branch->location_note,
                    'latitude' => $branch->latitude,
                    'longitude' => $branch->longitude,
                    'distance_km' => isset($branch->distance) ? round($branch->distance, 2) : null,
                    'categories' => $branch->foodCategories->map(fn($foodCat) => $foodCat->category->name)->unique()->values()
                ];
            }),
        ]);
    }

    protected function getBranchesWithoutLocation($category)
    {
        return Branch::whereHas('foodCategories.category', function($q) use ($category) {
            $q->where('categories.id', $category->id);
        })
            ->with(['restaurant:id,name', 'foodCategories' => function($query) {
                $query->with('category:id,name');
            }])
            ->get()
            ->map(function ($branch) {
                return [
                    'branch_id' => $branch->id,
                    'restaurant' => $branch->restaurant->name ?? null,
                    'photo' => $branch->photo,
                    'description' => $branch->description,
                    'location_note' => $branch->location_note,
                    'latitude' => $branch->latitude,
                    'longitude' => $branch->longitude,
                    'distance_km' => null,
                    'categories' => $branch->foodCategories->map(fn($foodCat) => $foodCat->category->name)->unique()->values()
                ];
            });
    }

}
