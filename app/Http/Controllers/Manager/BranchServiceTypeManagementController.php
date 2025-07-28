<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchServiceTypeRequest;
use Illuminate\Http\Request;
use App\Services\Manager\BranchServiceTypeService;

class BranchServiceTypeManagementController extends Controller
{
    protected $service;

    public function __construct(BranchServiceTypeService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->getAllForManager();

        return response()->json(['data' => $items]);
    }

    public function store(StoreBranchServiceTypeRequest  $request)
    {
        
        $data = $request->validated();

        $item = $this->service->create($data);

        if (!$item) {
            return response()->json(['error' => 'غير مصرح لك بإضافة خدمة لهذا الفرع'], 403);
        }

        return response()->json(['message' => 'تمت الإضافة بنجاح', 'data' => $item], 201);
    }

    public function show($id)
    {
        $item = $this->service->getByIdForManager($id);

        if (!$item) {
            return response()->json(['error' => 'غير مصرح بعرض هذا العنصر أو غير موجود'], 404);
        }

        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'custom_price' => 'nullable|numeric|min:0',
            'service_cost' => 'nullable|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $item = $this->service->update($id, $data);

        if (!$item) {
            return response()->json(['error' => 'غير مصرح بالتعديل على هذا العنصر'], 403);
        }

        return response()->json(['message' => 'تم التحديث بنجاح', 'data' => $item]);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (!$deleted) {
            return response()->json(['error' => 'غير مصرح بالحذف أو غير موجود'], 403);
        }

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
