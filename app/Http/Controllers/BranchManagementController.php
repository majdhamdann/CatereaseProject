<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BranchManagementService;
class BranchManagementController extends Controller
{
    protected $branchService;

    public function __construct(BranchManagementService $branchService)
    {
        $this->branchService = $branchService;
    }
    public function getAllBranchesWithDetails()
    {
        $results = $this->branchService->getAllBranchesWithDetails();
        return response()->json($results, 200);
    }


}
