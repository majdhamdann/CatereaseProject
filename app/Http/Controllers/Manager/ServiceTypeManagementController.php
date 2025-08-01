<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchServiceType;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceTypeManagementController extends Controller
{
    public function index()
    {
        return response()->json(ServiceType::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pricing_model' => 'required|in:fixed,per_person,per_hour',
            'is_active' => 'boolean',
        ]);

        $serviceType = ServiceType::create($data);

        return response()->json(['message' => 'Service Type created', 'service_type' => $serviceType], 201);
    }

    public function show($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        return response()->json($serviceType);
    }

    public function update(Request $request, $id)
    {
        $serviceType = ServiceType::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'pricing_model' => 'sometimes|in:fixed,per_person,per_hour',
            'is_active' => 'boolean',
        ]);

        $serviceType->update($data);

        return response()->json(['message' => 'Service Type updated', 'service_type' => $serviceType]);
    }

    public function destroy($id)
    {
        $serviceType = ServiceType::findOrFail($id);
        $serviceType->delete();

        return response()->json(['message' => 'Service Type deleted']);
    }
    public function getBranchServiceTypes()
{
        $manager = auth()->user();
        $branchId = Branch::where('manager_id', $manager->id)->value('id');

    if (!$branchId) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير.'
        ], 403);
    }

    $serviceTypes = BranchServiceType::with('serviceType')
                    ->where('branch_id', $branchId)
                    ->get();

    return response()->json([
        'status' => true,
        'service_types' => $serviceTypes
    ]);
}

}
