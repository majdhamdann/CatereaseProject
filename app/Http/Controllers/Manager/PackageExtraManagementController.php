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
                'extras.*.name' => 'nullable|string',
                'extras.*.price' => 'nullable|numeric',
                'extras.*.is_optional' => 'nullable|boolean',
            ]);

            foreach ($request->extras as $extra) {
                PackageExtra::create([
                    'package_id' => $request->package_id,
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

            $data = $request->only(['name', 'price', 'is_optional']);
            $extra->update($data);

            return response()->json(['message' => 'تم التحديث بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'خطأ أثناء التحديث', 'error' => $e->getMessage()], 500);
        }
    }

    // حذف زيادة
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
