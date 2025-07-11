<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageItem;
use Illuminate\Http\Request;

class PackageItemManagementController extends Controller
{
    public function index($packageId)
    {
        try {
            $items = PackageItem::with('foodItem')
                ->where('package_id', $packageId)
                ->get();

            return response()->json($items);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء جلب العناصر', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (!isset($request->package_id) || !is_numeric($request->package_id)) {
                return response()->json(['message' => 'package_id مطلوب ويجب أن يكون رقمًا'], 422);
            }

            if (!isset($request->items) || !is_array($request->items) || count($request->items) === 0) {
                return response()->json(['message' => 'items يجب أن تكون مصفوفة تحتوي على عناصر'], 422);
            }

            foreach ($request->items as $item) {
                if (
                    !isset($item['food_item_id']) || !is_numeric($item['food_item_id']) ||
                    !isset($item['quantity']) || !is_numeric($item['quantity'])
                ) {
                    continue;
                }
                $existing = PackageItem::where('package_id', $request->package_id)
                    ->where('food_item_id', $item['food_item_id'])
                    ->first();

                if (!$existing) {
                    PackageItem::create([
                        'package_id' => $request->package_id,
                        'food_item_id' => $item['food_item_id'],
                        'quantity' => $item['quantity'],
                        'is_optional' => $item['is_optional'] ?? false,
                    ]);
                }
            }

            return response()->json(['message' => 'تمت إضافة العناصر بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء الإضافة', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $package = Package::with('items.foodItem')->findOrFail($id);
            return response()->json($package);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء عرض الباقة', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = PackageItem::findOrFail($id);

            $data = [];

            if ($request->has('quantity') && is_numeric($request->quantity)) {
                $data['quantity'] = $request->quantity;
            }

            if ($request->has('is_optional')) {
                $data['is_optional'] = filter_var($request->is_optional, FILTER_VALIDATE_BOOLEAN);
            }

            if (empty($data)) {
                return response()->json(['message' => 'لا توجد بيانات صالحة للتحديث'], 422);
            }

            $item->update($data);

            return response()->json(['message' => 'تم التحديث بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء التحديث', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = PackageItem::findOrFail($id);
            $item->delete();

            return response()->json(['message' => 'تم الحذف بنجاح']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'حدث خطأ أثناء الحذف', 'error' => $e->getMessage()], 500);
        }
    }
}
