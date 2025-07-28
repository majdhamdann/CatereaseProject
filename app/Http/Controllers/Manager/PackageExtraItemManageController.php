<?php

namespace App\Http\Controllers\Manager;


use App\Http\Controllers\Controller;
use App\Http\Requests\packageManagement\StorePackageRequest;
use App\Http\Requests\packageManagement\UpdatePackagemanageRequest;
use App\Http\Requests\packageManagement\StorePackageItemRequest;
use App\Http\Requests\packageManagement\StorePackageExtraRequest;
use App\Models\Branch;
use App\Models\Package;
use App\Models\PackageExtra;
use App\Models\PackageItem;
use App\Services\Manager\PackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PackageExtraItemManageController extends Controller
{
  public function index()
{
    $branch = Branch::where('manager_id', Auth::id())->first();

    if (!$branch) {
        return response()->json(['error' => 'No branch found for this manager'], 403);
    }

    $packages = Package::with([
        'items.foodItem',
        'extras',
        'categories',
        'occasionTypes',
        'branchServiceType',
        'branch',
        'discounts',
        'coupons',
        'extraServices',
        'feedbacks'
    ])
    ->where('branch_id', $branch->id)
    ->get();

    return response()->json(['packages' => $packages]);
}



   public function show($id)
{
    $branch = Branch::where('manager_id', Auth::id())->first();

    if (!$branch) {
        return response()->json(['error' => 'No branch found for this manager'], 403);
    }

    $package = Package::with([
        'items.foodItem',
        'extras',
        'categories',
        'occasionTypes',
        'branchServiceType',
        'branch',
        'discounts',
        'coupons',
        'extraServices',
        'feedbacks'
    ])
    ->where('branch_id', $branch->id)
    ->findOrFail($id);

    return response()->json(['package' => $package]);
}


   public function store(Request $request)
{
    $branch = Branch::where('manager_id', Auth::id())->first();

    if (!$branch) {
        return response()->json(['error' => 'No branch found for this manager'], 403);
    }

    $validated = $request->validate([
        'branch_service_type_ids' => 'required|array',
        'branch_service_type_ids.*' => 'exists:branch_service_types,id',
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'photo' => 'required|string',
        'base_price' => 'required|numeric',
        'serves_count' => 'required|integer',
        'max_extra_persons' => 'required|integer',
        'price_per_extra_person' => 'required|numeric',
        'cancellation_policy' => 'required|string',
        'prepayment_required' => 'boolean',
        'prepayment_amount' => 'required|numeric',
        'category_ids' => 'required|array',
        'category_ids.*' => 'exists:categories,id',
        'is_active' => 'boolean',
        'notes' => 'nullable|string',
        'occasion_type_ids' => 'required|array',
        'occasion_type_ids.*' => 'exists:occasion_types,id',
        'items' => 'array',
        'items.*.food_item_id' => 'required|exists:food_items,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.is_optional' => 'boolean',
        'extras' => 'array',
        'extras.*.type' => 'required|in:food_item,service,simple',
        'extras.*.name' => 'required|string',
        'extras.*.price' => 'required|numeric',
        'extras.*.is_optional' => 'boolean',
        'extras.*.food_item_id' => 'required|exists:food_items,id',
        'extras.*.branch_service_type_id' => 'required|exists:branch_service_types,id',
    ]);

    DB::beginTransaction();

    try {
        // نضيف branch_id يدويًا هنا
        $package = Package::create([
            'branch_id' => $branch->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'photo' => $validated['photo'],
            'base_price' => $validated['base_price'],
            'serves_count' => $validated['serves_count'],
            'max_extra_persons' => $validated['max_extra_persons'],
            'price_per_extra_person' => $validated['price_per_extra_person'],
            'cancellation_policy' => $validated['cancellation_policy'],
            'prepayment_required' => $validated['prepayment_required'],
            'prepayment_amount' => $validated['prepayment_amount'],
            'is_active' => $validated['is_active'] ?? true,
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] ?? [] as $item) {
            PackageItem::create([
                'package_id' => $package->id,
                'food_item_id' => $item['food_item_id'],
                'quantity' => $item['quantity'],
                'is_optional' => $item['is_optional'] ?? false,
            ]);
        }

        foreach ($validated['extras'] ?? [] as $extra) {
            PackageExtra::create([
                'package_id' => $package->id,
                'type' => $extra['type'],
                'name' => $extra['name'],
                'price' => $extra['price'],
                'is_optional' => $extra['is_optional'] ?? true,
                'food_item_id' => $extra['food_item_id'] ?? null,
                'branch_service_type_id' => $extra['branch_service_type_id'] ?? null,
            ]);
        }

        $package->categories()->sync($validated['category_ids']);
        $package->occasionTypes()->sync($validated['occasion_type_ids']);
        $package->extraServices()->sync($validated['branch_service_type_ids']);

        DB::commit();

        $package->load([
            'items.foodItem',
            'extras.foodItem',
            'extras.branchServiceType',
            'categories',
            'occasionTypes',
            'extraServices',
        ]);

        return response()->json(['message' => 'Package created', 'package' => $package], 201);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to create package', 'message' => $e->getMessage()], 500);
    }
}


    public function update(Request $request, $id)
    {
        $branch = Branch::where('manager_id', Auth::id())->first();

        if (!$branch) {
           return response()->json(['error' => 'No branch found for this manager'], 403);
       }

           $package = Package::where('branch_id', $branch->id)->findOrFail($id);


       $validated = $request->validate([
         'branch_id' => 'sometimes|exists:branches,id',
         'branch_service_type_ids' => 'sometimes|array',
         'branch_service_type_ids.*' => 'exists:branch_service_types,id',
         'name' => 'sometimes|string|max:255',
         'description' => 'sometimes|string',
         'photo' => 'sometimes|string',
         'base_price' => 'sometimes|numeric',
         'serves_count' => 'sometimes|integer',
         'max_extra_persons' => 'sometimes|integer',
         'price_per_extra_person' => 'sometimes|numeric',
         'cancellation_policy' => 'sometimes|string',
         'prepayment_required' => 'sometimes|boolean',
         'prepayment_amount' => 'sometimes|numeric',
         'category_ids' => 'sometimes|array',
         'category_ids.*' => 'exists:categories,id',
         'is_active' => 'sometimes|boolean',
         'notes' => 'sometimes|nullable|string',
         'occasion_type_ids' => 'sometimes|array',
         'occasion_type_ids.*' => 'exists:occasion_types,id',
         'items' => 'sometimes|array',
         'items.*.food_item_id' => 'required_with:items|exists:food_items,id',
         'items.*.quantity' => 'required_with:items|integer|min:1',
         'items.*.is_optional' => 'sometimes|boolean',
         'extras' => 'sometimes|array',
         'extras.*.type' => 'required_with:extras|in:food_item,service,simple',
         'extras.*.name' => 'required_with:extras|string',
         'extras.*.price' => 'required_with:extras|numeric',
         'extras.*.is_optional' => 'sometimes|boolean',
         'extras.*.food_item_id' => 'required_with:extras|exists:food_items,id',
         'extras.*.branch_service_type_id' => 'required_with:extras|exists:branch_service_types,id',
]);

        DB::beginTransaction();

        try {
            $package->update($request->only([
                'name', 'description', 'photo', 'base_price', 'serves_count',
                'max_extra_persons', 'price_per_extra_person', 'cancellation_policy',
                'prepayment_required', 'prepayment_amount', 'is_active', 'notes'
            ]));

            if ($request->has('items')) {
                $package->items()->delete();
                foreach ($validated['items'] as $item) {
                    PackageItem::create([
                        'package_id' => $package->id,
                        'food_item_id' => $item['food_item_id'],
                        'quantity' => $item['quantity'],
                        'is_optional' => $item['is_optional'] ?? false,
                    ]);
                }
            }

            if ($request->has('extras')) {
                $package->extras()->delete();
                foreach ($validated['extras'] as $extra) {
                    PackageExtra::create([
                        'package_id' => $package->id,
                        'type' => $extra['type'],
                        'name' => $extra['name'],
                        'price' => $extra['price'],
                        'is_optional' => $extra['is_optional'] ?? true,
                        'food_item_id' => $extra['food_item_id'] ?? null,
                        'branch_service_type_id' => $extra['branch_service_type_id'] ?? null,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Package updated', 'package' => $package]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update package', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
{
    $branch = Branch::where('manager_id', Auth::id())->first();

    if (!$branch) {
        return response()->json(['error' => 'No branch found for this manager'], 403);
    }

    $package = Package::where('branch_id', $branch->id)->findOrFail($id);

    $package->delete();

    return response()->json(['message' => 'Package deleted']);
}

}