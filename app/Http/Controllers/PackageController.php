<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use App\Services\PackageService;

class PackageController extends Controller
{
    protected $PackageService;

    public function __construct(PackageService $PackageService)
    {
        $this->PackageService = $PackageService;
    }

    public function index($branchId)
    {
        try {
            $result = $this->PackageService->getBranchPackages($branchId);

            if (!$result['status']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], $result['code']);
            }

            return response()->json([
                'status' => 'success',
                ...$result['data']
            ], $result['code']);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $packageData = $this->PackageService->getPackageById($id);

        if (!$packageData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Package not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $packageData,
        ], 200);
    }
    public function listPackages()
    {
        try {
            $packages = $this->PackageService->getAllActivePackages();

            return response()->json([
                'status' => 'success',
                'data' => $packages
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch packages',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
