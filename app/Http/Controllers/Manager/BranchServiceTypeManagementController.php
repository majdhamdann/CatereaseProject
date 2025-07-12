<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchServiceTypeManagementController extends Controller
{
    public function index()
    {
        $managerId = Auth::id();

        $branchServiceTypes = BranchServiceType::whereHas('branch', function ($query) use ($managerId) {
            $query->where('manager_id', $managerId);
        })->with('branch')->get();

        return response()->json(['data' => $branchServiceTypes]);
    }

    public function store(Request $request)
    {
        $managerId = Auth::id();

        $data = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'service_type_id' => 'required|exists:service_types,id',
            'custom_price' => 'required|numeric|min:0',
            'service_cost' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        $branch = Branch::where('id', $data['branch_id'])
                        ->where('manager_id', $managerId)
                        ->first();

        if (!$branch) {
            return response()->json(['error' => 'غير مصرح لك بإضافة خدمة لهذا الفرع'], 403);
        }

        $item = BranchServiceType::create($data);

        return response()->json(['message' => 'تمت الإضافة بنجاح', 'data' => $item], 201);
    }

    public function show($id)
    {
        $managerId = Auth::id();

        $item = BranchServiceType::where('id', $id)
            ->whereHas('branch', function ($query) use ($managerId) {
                $query->where('manager_id', $managerId);
            })
            ->first();

        if (!$item) {
            return response()->json(['error' => 'غير مصرح بعرض هذا العنصر أو غير موجود'], 404);
        }

        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $managerId = Auth::id();

        $item = BranchServiceType::where('id', $id)
            ->whereHas('branch', function ($query) use ($managerId) {
                $query->where('manager_id', $managerId);
            })
            ->first();

        if (!$item) {
            return response()->json(['error' => 'غير مصرح بالتعديل على هذا العنصر'], 403);
        }

        $data = $request->validate([
            'custom_price' => 'nullable|numeric|min:0',
            'service_cost' => 'nullable|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $item->update($data);

        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $item]);
    }

    public function destroy($id)
    {
        $managerId = Auth::id();

        $item = BranchServiceType::where('id', $id)
            ->whereHas('branch', function ($query) use ($managerId) {
                $query->where('manager_id', $managerId);
            })
            ->first();

        if (!$item) {
            return response()->json(['error' => 'غير مصرح بالحذف أو غير موجود'], 403);
        }

        $item->delete();

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
