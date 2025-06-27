<?php

namespace App\Repositories;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\BranchRepositoryInterface;

class BranchRepository implements BranchRepositoryInterface
{
    public function getAllWithRelations()
    {
        return Branch::with('restaurant', 'city')->get();
    }

    public function getNearby($lat, $lng)
    {
        return Branch::select('*', DB::raw("
            (6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            )) AS distance
        "))
            ->setBindings([$lat, $lng, $lat])
            ->with('restaurant', 'city')
            ->orderBy('distance')
            ->limit(10)
            ->get();
    }

    public function getByCityId($cityId)
    {
        return Branch::where('city_id', $cityId)
            ->with('restaurant', 'city')
            ->limit(10)
            ->get();
    }
}
