<?php

namespace App\Http\Controllers\Manager;


use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FoodItem;
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
    $manager = auth()->user();
    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['error' => 'لا يوجد فرع مرتبط بهذا المدير'], 403);
    }

    $package = Package::with([
        'items.foodItem',
        'extras',
        'categories',
        'occasionTypes',
        'branch',
        'discounts',
        'coupons',
        'extraServices',
        'feedbacks.user',
    ])
    ->where('branch_id', $branch->id)
    ->findOrFail($id);
    return response()->json([
        'id' => $package->id,
        'name' => $package->name,
        'description' => $package->description,
        'photo' => $package->photo,
        'base_price' => $package->base_price,
        'serves_count' => $package->serves_count,
        'max_extra_persons' => $package->max_extra_persons,
        'price_per_extra_person' => $package->price_per_extra_person,
        'is_active' => $package->is_active,
        'notes' => $package->notes,
        'cancellation_policy' => $package->cancellation_policy,
        'prepayment_required' => $package->prepayment_required,
        'prepayment_amount' => $package->prepayment_amount,
        'name_branch' => $package->branch->restaurant->name,
        'branch_id' => $package->branch->id,
        'occasion_types' => $package->occasionTypes->map(function ($occasionTypes) {
            return [
                'id' => $occasionTypes->id,
                'name' => $occasionTypes->name,
            ];
        }),
       'branch_service_types' => $package->extraServices->map(function ($bst) {
         return [
            'id' => $bst->id,
            'service_type_id' => $bst->service_type_id,
            'service_type_name' => $bst->serviceType->name ?? null,
            'custom_price' => $bst->custom_price,
            'service_cost' => $bst->service_cost,
            'is_available' => $bst->is_available,
           ];
       }),

        'items' => $package->items->map(function ($item) {
            return [
                'id' => $item->id,
                'food_item_name' => $item->foodItem->name ?? null,
                'quantity' => $item->quantity,
                'is_optional' => $item->is_optional,
            ];
        }),
        'categories' => $package->categories->map(function ($categories) {
            return [
                'id' => $categories->id,
                'name' => $categories->name,
            ];
        }),


        'extras' => $package->extras->map(function ($extra) {
            return [
                'id' => $extra->id,
                'name' => $extra->name,
                'price' => $extra->price,
            ];
        }),

        'extra_services' => $package->extraServices->map(function ($service) {
              return [
                'name' => $service->servicetype->name, 
                'price' => $service->custom_price,     
             ];
        }),


        'discounts' => $package->discounts->map(function ($discount) {
            return [
                'id' => $discount->id,
                'amount' => $discount->value,
                'start_date' => $discount->start_date,
                'end_date' => $discount->end_date,
            ];
        }),

        'coupons' => $package->coupons->map(function ($coupon) {
            return [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
                'expiration_date' => $coupon->expiration_date,
            ];
        }),

        'feedbacks' => $package->feedbacks->map(function ($feedback) {
           $averageRating = $feedback->avg('score') ?? 0;

            return [
                'id' => $feedback->id,
                'rating' => $feedback->rating,
                'average_rating' => round($averageRating, 2),
                'comment' => $feedback->comment,
                'user_name' => $feedback->user->name ?? null,
                'created_at' => $feedback->created_at->toDateTimeString(),
            ];
        }),
    ]);
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
        'is_active' => 'required|boolean',
        'notes' => 'nullable|string',
        'occasion_type_ids' => 'required|array',
        'occasion_type_ids.*' => 'exists:occasion_types,id',

        'items' => 'array',
        'items.*.food_item_name' => 'required|string',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.is_optional' => 'boolean',

        'extras' => 'array',
        'extras.*.name' => 'required|string',
        'extras.*.price' => 'required|numeric',
    ]);

    DB::beginTransaction();

    try {
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
            $foodItem = FoodItem::firstOrCreate(
                ['name' => $item['food_item_name'], 'branch_id' => $branch->id],
                [
                    'food_category_id' => 1, 
                    'description' => 'Auto-created item.',
                    'price' => 0.0,
                    'discount_price' => null,
                    'photo' => '',
                    'available' => true,
                    'type' => null,
                ]
            );

            PackageItem::create([
                'package_id' => $package->id,
                'food_item_id' => $foodItem->id,
                'quantity' => $item['quantity'],
                'is_optional' => $item['is_optional'] ?? false,
            ]);
        }

        foreach ($validated['extras'] ?? [] as $extra) {
            $foodItem = FoodItem::firstOrCreate(
                ['name' => $extra['name'], 'branch_id' => $branch->id],
                [
                    'food_category_id' => 1, 
                    'description' => 'Auto-created extra item.',
                    'price' => $extra['price'],
                    'discount_price' => null,
                    'photo' => '',
                    'available' => true,
                    'type' => null,
                ]
            );

            PackageExtra::create([
                'package_id' => $package->id,
                'type' => 'food_item',
                'name' => $extra['name'],
                'price' => $extra['price'],
                'is_optional' => true,
                'food_item_id' => $foodItem->id,
                'branch_service_type_id' => null,
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
        'items.*.food_item_name' => 'required_with:items|string',
        'items.*.quantity' => 'required_with:items|integer|min:1',
        'items.*.is_optional' => 'sometimes|boolean',

        'extras' => 'sometimes|array',
        'extras.*.name' => 'required_with:extras|string',
        'extras.*.price' => 'required_with:extras|numeric',
    ]);

    DB::beginTransaction();

    try {
        $package->update($request->only([
            'name', 'description', 'photo', 'base_price', 'serves_count',
            'max_extra_persons', 'price_per_extra_person', 'cancellation_policy',
            'prepayment_required', 'prepayment_amount', 'is_active', 'notes'
        ]));

        // Update Items
        if (isset($validated['items'])) {
            $package->items()->delete();
            foreach ($validated['items'] as $item) {
                $foodItem = FoodItem::firstOrCreate(
                    ['name' => $item['food_item_name'], 'branch_id' => $branch->id],
                    [
                        'food_category_id' => 1,
                        'description' => 'Auto-created item.',
                        'price' => 0.0,
                        'discount_price' => null,
                        'photo' => '',
                        'available' => true,
                        'type' => null,
                    ]
                );

                PackageItem::create([
                    'package_id' => $package->id,
                    'food_item_id' => $foodItem->id,
                    'quantity' => $item['quantity'],
                    'is_optional' => $item['is_optional'] ?? false,
                ]);
            }
        }

        // Update Extras
        if (isset($validated['extras'])) {
            $package->extras()->delete();
            foreach ($validated['extras'] as $extra) {
                $foodItem = FoodItem::firstOrCreate(
                    ['name' => $extra['name'], 'branch_id' => $branch->id],
                    [
                        'food_category_id' => 1,
                        'description' => 'Auto-created extra item.',
                        'price' => $extra['price'],
                        'discount_price' => null,
                        'photo' => '',
                        'available' => true,
                        'type' => null,
                    ]
                );

                PackageExtra::create([
                    'package_id' => $package->id,
                    'type' => 'food_item',
                    'name' => $extra['name'],
                    'price' => $extra['price'],
                    'is_optional' => true,
                    'food_item_id' => $foodItem->id,
                    'branch_service_type_id' => null,
                ]);
            }
        }

        // Update Pivot Tables
        if (isset($validated['category_ids'])) {
            $package->categories()->sync($validated['category_ids']);
        }

        if (isset($validated['occasion_type_ids'])) {
            $package->occasionTypes()->sync($validated['occasion_type_ids']);
        }

        if (isset($validated['branch_service_type_ids'])) {
            $package->extraServices()->sync($validated['branch_service_type_ids']);
        }

        DB::commit();

        $package->load([
            'items.foodItem',
            'extras.foodItem',
            'extras.branchServiceType',
            'categories',
            'occasionTypes',
            'extraServices',
        ]);

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