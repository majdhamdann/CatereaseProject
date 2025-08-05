<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\BranchDeliveryArea;
use App\Models\City;
use Illuminate\Support\Facades\Auth;

class BranchDeliveryAreaController extends Controller
{
    // ✅ عرض جميع مناطق التوصيل لفرع معين
    public function index($branchId)
    {
        $owner = Auth::user();

        $branch = Branch::where('id', $branchId)
                        ->whereHas('restaurant', function ($q) use ($owner) {
                            $q->where('owner_id', $owner->id);
                        })->firstOrFail();

        $areas = BranchDeliveryArea::with('city')
                    ->where('branch_id', $branch->id)
                    ->get();

        return response()->json($areas);
    }

    // ✅ إضافة منطقة توصيل لفرع
    public function store(Request $request, $branchId)
{
    $request->validate([
        'city_name' => 'required|string|max:255',
        'country' => 'required|string|max:255',
        'delivery_price' => 'required|numeric|min:0',
    ]);

    $owner = Auth::user();

    // التأكد أن الفرع يخص مطعم يملكه هذا المالك
    $branch = Branch::where('id', $branchId)
                    ->whereHas('restaurant', function ($q) use ($owner) {
                        $q->where('owner_id', $owner->id);
                    })->firstOrFail();

    // البحث أو الإنشاء للمدينة
    $city = City::firstOrCreate(
        [
            'name' => $request->city_name,
            'country' => $request->country,
        ]
    );

    // التحقق من عدم تكرار المدينة لنفس الفرع
    $exists = BranchDeliveryArea::where('branch_id', $branch->id)
                ->where('city_id', $city->id)
                ->exists();

    if ($exists) {
        return response()->json(['message' => 'هذه المدينة مضافة مسبقًا لهذا الفرع.'], 422);
    }

    // إنشاء منطقة التوصيل
    $area = BranchDeliveryArea::create([
        'branch_id' => $branch->id,
        'city_id' => $city->id,
        'delivery_price' => $request->delivery_price,
    ]);

    return response()->json([
        'message' => 'تمت إضافة منطقة التوصيل بنجاح.',
        'area' => $area
    ]);
}

    // ✅ تعديل سعر التوصيل لمنطقة
    public function update(Request $request, $areaId)
    {
        $request->validate([
            'delivery_price' => 'required|numeric|min:0',
        ]);

        $owner = Auth::user();

        $area = BranchDeliveryArea::findOrFail($areaId);

        // التأكد من ملكية الفرع للمطعم
        $this->authorizeOwner($owner->id, $area->branch_id);

        $area->update([
            'delivery_price' => $request->delivery_price
        ]);

        return response()->json(['message' => 'تم التحديث بنجاح', 'area' => $area]);
    }

    // ✅ حذف منطقة توصيل
    public function destroy($areaId)
    {
        $owner = Auth::user();

        $area = BranchDeliveryArea::findOrFail($areaId);

        $this->authorizeOwner($owner->id, $area->branch_id);

        $area->delete();

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }

    protected function authorizeOwner($ownerId, $branchId)
    {
        $branch = Branch::where('id', $branchId)
                        ->whereHas('restaurant', function ($q) use ($ownerId) {
                            $q->where('owner_id', $ownerId);
                        })->first();

        if (!$branch) {
            abort(403, 'أنت لا تملك هذا الفرع');
        }
    }
}
