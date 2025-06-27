<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
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


}
