<?php


namespace App\Services;

use App\Models\Restaurant;

class RestaurantService
{
    public function getRestaurantsWithDetails()
    {
        $restaurants = Restaurant::with([
            'branches.categories.foodCategory',
            'feedbacks' => function ($q) {
                $q->where('type', 'rating');
            }
        ])->get();

        return $restaurants->map(function ($restaurant) {
            $ratings = $restaurant->feedbacks;
            $averageRating = round($ratings->avg('score') ?? 0, 1);
            $totalRatings = $ratings->count();

            return [
                'restaurant_id' => $restaurant->id,
                'name' => $restaurant->name,
                'photo' => $restaurant->photo,
                'description' => $restaurant->description,
                'rating' => $averageRating,
                'total_ratings' => $totalRatings,
                'branches' => $restaurant->branches->map(function ($branch) {
                    return [
                        'branch_id' => $branch->id,
                        'location' => $branch->location ?? null,
                        'categories' => $branch->categories
                            ->pluck('foodCategory.name')
                            ->filter()
                            ->unique()
                            ->values()
                    ];
                })
            ];
        });
    }
}

