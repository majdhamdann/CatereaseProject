<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\BranchRepositoryInterface;
use Illuminate\Support\Facades\Log;

class BranchService{
     public function getAllBranchesWithDetails()
    {
        $branches = Branch::with([
            'restaurant:id,name,description',
            'categories:id,branch_id,name',
            'restaurant.feedbacks'
        ])->get(['id', 'restaurant_id', 'photo', 'description']);

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
                'photo' => $branch->photo,
                'restaurant_name' => $restaurant->name ?? null,
                'categories' => $branch->categories->pluck('name'),
                'total_ratings' => $ratingCount,
                'averageRating' => $averageRating
            ];
        });
    }


    protected $branchRepository;

    public function __construct(BranchRepositoryInterface $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }



    public function getNearbyBranches(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $cityId = $request->query('city_id');

        if ($lat && $lng) {
            return $this->branchRepository->getNearby($lat, $lng);
        } elseif ($cityId) {
            return $this->branchRepository->getByCityId($cityId);
        }

        return null;
    }



    public function getBranchItems(int $branchId): array
    {
        try {
            DB::beginTransaction();

            $branch = $this->branchRepository->getAvailableItemsByBranch($branchId);

            if (!$branch) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Branch not found.'
                ];
            }

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'data' => [
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->description,
                    'items' => $branch->foodItems->map([$this, 'formatFoodItem']),
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Branch item fetch failed', [
                'branch_id' => $branchId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Internal Server Error'
            ];
        }
    }

    public function formatFoodItem($item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'price' => $item->price,
            'discount_price' => $item->discount_price,
            'type' => $item->type,
            'photo' => $item->photo,
            'calories' => $item->calories,
            'category' => $item->category?->foodCategory?->name,
        ];
    }


}
