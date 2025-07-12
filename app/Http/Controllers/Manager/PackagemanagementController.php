<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackagemanagementController extends Controller
{
    public function index()
    {
        $managerId = Auth::id();

        $packages = Package::whereHas('branch', function ($query) use ($managerId) {
            $query->where('manager_id', $managerId);
        })->with(['branch', 'occasionType', 'categories'])
         ->get();

        return response()->json(['packages' => $packages]);
    }

    public function store(Request $request)
    {
        $managerId = Auth::id();

        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'branch_service_type_id' => 'nullable|exists:branch_service_types,id',
            'occasion_type_id' => 'nullable|exists:occasion_types,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'base_price' => 'required|numeric|min:0',
            'serves_count' => 'required|integer|min:0',
            'max_extra_persons' => 'nullable|integer|min:0',
            'price_per_extra_person' => 'nullable|numeric|min:0',
            'cancellation_policy' => 'nullable|string',
            'prepayment_required' => 'boolean',
            'prepayment_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $branch = Branch::where('id', $data['branch_id'])
                        ->where('manager_id', $managerId)
                        ->first();

        if (!$branch) {
            return response()->json(['error' => 'Unauthorized to create package for this branch'], 403);
        }

        $package = Package::create($data);
        if ($request->has('category_ids')) {
            $package->categories()->sync($request->category_ids);
        }

        return response()->json(['message' => 'Package created', 'package' => $package], 201);
    }

    public function show($id)
    {
        $managerId = Auth::id();

        $package = Package::where('id', $id)
            ->whereHas('branch', function ($q) use ($managerId) {
                $q->where('manager_id', $managerId);
            })
            ->with(['branch', 'occasionType', 'categories'])

            ->first();

        if (!$package) {
            return response()->json(['error' => 'Package not found or unauthorized'], 404);
        }

        return response()->json(['package' => $package]);
    }

    public function update(Request $request, $id)
    {
        $managerId = Auth::id();

        $package = Package::where('id', $id)
            ->whereHas('branch', function ($q) use ($managerId) {
                $q->where('manager_id', $managerId);
            })
            ->first();

        if (!$package) {
            return response()->json(['error' => 'Unauthorized to update this package'], 403);
        }

        $data = $request->validate([
            'branch_id' => 'sometimes|exists:branches,id',
            'branch_service_type_id' => 'nullable|exists:branch_service_types,id',
            'occasion_type_id' => 'nullable|exists:occasion_types,id',
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|string',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',

            'base_price' => 'nullable|numeric|min:0',
            'serves_count' => 'nullable|integer|min:0',
            'max_extra_persons' => 'nullable|integer|min:0',
            'price_per_extra_person' => 'nullable|numeric|min:0',
            'cancellation_policy' => 'nullable|string',
            'prepayment_required' => 'boolean',
            'prepayment_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if (isset($data['branch_id'])) {
            $branch = Branch::where('id', $data['branch_id'])
                ->where('manager_id', $managerId)
                ->first();

            if (!$branch) {
                return response()->json(['error' => 'Unauthorized to assign this branch'], 403);
            }
        }

        $package->update($data);
        if ($request->has('category_ids')) {
           $package->categories()->sync($request->category_ids);  
        }

        return response()->json(['message' => 'Package updated', 'package' => $package]);
    }

    public function destroy($id)
    {
        $managerId = Auth::id();

        $package = Package::where('id', $id)
            ->whereHas('branch', function ($q) use ($managerId) {
                $q->where('manager_id', $managerId);
            })
            ->first();

        if (!$package) {
            return response()->json(['error' => 'Unauthorized to delete this package'], 403);
        }

        $package->delete();

        return response()->json(['message' => 'Package deleted']);
    }
}
