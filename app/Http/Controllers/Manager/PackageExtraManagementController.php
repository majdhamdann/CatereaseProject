<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageExtra;
use Illuminate\Http\Request;

class PackageExtraManagementController extends Controller
{
    public function index($packageId)
    {
        try {
            $extras = PackageExtra::where('package_id', $packageId)->get();

            if ($extras->isEmpty()) {
                return response()->json(['message' => 'لا توجد زيادات لهذا الباكج بعد'], 404);
            }

            return response()->json(['package_id' => $packageId, 'extras' => $extras]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل في جلب الزيادات', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:packages,id',
                'extras' => 'required|array|min:1',
                'extras.*.type' => 'nullable|string',
                'extras.*.food_item_id' => 'nullable|exists:food_items,id',
                'extras.*.branch_service_type_id' => 'nullable|exists:branch_service_types,id',
                'extras.*.name' => 'nullable|string',
                'extras.*.price' => 'nullable|numeric',
                'extras.*.is_optional' => 'nullable|boolean',
            ]);

            foreach ($request->extras as $extra) {
                PackageExtra::create([
                    'package_id' => $request->package_id,
                    'type' => $extra['type'] ?? null,
                    'food_item_id' => $extra['food_item_id'] ?? null,
                    'branch_service_type_id' => $extra['branch_service_type_id'] ?? null,
                    'name' => $extra['name'] ?? 'زيادة غير مسماة',
                    'price' => $extra['price'] ?? 0,
                    'is_optional' => $extra['is_optional'] ?? true,
                ]);
            }

            return response()->json(['message' => 'تمت إضافة الزيادات بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل في الإضافة', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $extra = PackageExtra::findOrFail($id);

            $data = $request->validate([
                'type' => 'nullable|string',
                'food_item_id' => 'nullable|exists:food_items,id',
                'branch_service_type_id' => 'nullable|exists:branch_service_types,id',
                'name' => 'nullable|string',
                'price' => 'nullable|numeric',
                'is_optional' => 'nullable|boolean',
            ]);

            $extra->update($data);

            return response()->json(['message' => 'تم التحديث بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطأ أثناء التحديث', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $extra = PackageExtra::findOrFail($id);
            $extra->delete();

            return response()->json(['message' => 'تم الحذف بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطأ أثناء الحذف', 'error' => $e->getMessage()], 500);
        }
    }

    public function showPackageWithExtras($packageId)
    {
        try {
            $package = Package::with('extras')->findOrFail($packageId);
            return response()->json($package);
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل في جلب الباكج مع الزيادات', 'error' => $e->getMessage()], 500);
        }
    }
}
