<?php

namespace App\Services;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\BranchRepositoryInterface;

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
//
//
//     public function getNearbyBranches(Request $request)
//    {
//        $lat = $request->query('lat');
//        $lng = $request->query('lng');
//        $cityId = $request->query('city_id');
//
//        if ($lat && $lng) {
//            return Branch::select('*', DB::raw("
//            (6371 * acos(
//                cos(radians(?)) *
//                cos(radians(latitude)) *
//                cos(radians(longitude) - radians(?)) +
//                sin(radians(?)) *
//                sin(radians(latitude))
//            )) AS distance
//        "))
//                ->setBindings([$lat, $lng, $lat])
//                ->with('restaurant', 'city')
//                ->orderBy('distance')
//                ->limit(10)
//                ->get();
//
//        } elseif ($cityId) {
//            return Branch::where('city_id', $cityId)
//                ->with('restaurant', 'city')
//                ->limit(10)
//                ->get();
//        }
//
//        return null;
//    }

    protected $branchRepository;

    public function __construct(BranchRepositoryInterface $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

//    public function getAllBranchesWithDetails()
//    {
//        return $this->branchRepository->getAllWithRelations();
//    }

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
}
