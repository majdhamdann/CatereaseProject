<?php

namespace App\Services;

use App\Models\Branch;

class BranchManagerService{
     public function getAllBranchesWithDetails()
    {
        $branches = Branch::with([
            'restaurant:id,name,description',
            'categories:id,branch_id,name',
            'restaurant.feedbacks'
        ])->get(['id', 'restaurant_id', 'logo_url', 'description']);

        return $branches->map(function ($branch) {
            $ratingCount = 0;
            $averageRating = 0;
            $restaurant = $branch->restaurant;

            if ($restaurant && $restaurant->feedbacks) {
                $ratings = $restaurant->feedbacks->where('type', 'rating');
                $ratingCount = $ratings->count();
                $averageRating = round($ratings->avg('score') ?? 0, 1);
            }

            return [
                'branch_id' => $branch->id,
                'descriptionBranch' => $branch->description ?? null,
                'descriptionResraurant' => $restaurant->description ?? null,
                'logo_url' => $branch->logo_url,
                'restaurant_name' => $restaurant->name ?? null,
                'categories' => $branch->categories->pluck('name'),
                'total_ratings' => $ratingCount,
                'averageRating' => $averageRating
            ];
        });
    }
}