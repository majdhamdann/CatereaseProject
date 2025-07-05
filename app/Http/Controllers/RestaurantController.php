<?php

namespace App\Http\Controllers;


use App\Services\RestaurantService;
class RestaurantController extends Controller
{

    protected $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index()
    {
        $restaurants = $this->restaurantService->getRestaurantsWithDetails();
        return response()->json($restaurants);
    }
    public function getByCategory($name)
    {
        $restaurants = \App\Models\Restaurant::whereHas('branches.categories.foodCategory', function ($query) use ($name) {
            $query->where('name', $name);
        })
            ->with([
                'branches.categories.foodCategory',
                'feedbacks' => fn($q) => $q->where('type', 'rating'),
            ])
            ->get();

        return $restaurants->map(function ($restaurant) {
            $ratings = $restaurant->feedbacks;
            $averageRating = round($ratings->avg('score') ?? 0, 1);
            $ratingCount = $ratings->count();

            return [
                'restaurant_id' => $restaurant->id,
                'name' => $restaurant->name,
                'photo' => $restaurant->photo,
                'description' => $restaurant->description,
                'rating' => $averageRating,
                'total_ratings' => $ratingCount,
                'branches' => $restaurant->branches->map(function ($branch) {
                    return [
                        'branch_id' => $branch->id,
                        'location' => $branch->location,
                        'categories' => $branch->categories
                            ->pluck('foodCategory.name')
                            ->unique()
                            ->values(),
                    ];
                }),
            ];
        });
    }

}
