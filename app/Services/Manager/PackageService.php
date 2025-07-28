<?php
namespace App\Services\Manager;

use App\Models\Branch;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\PackageExtra;
use Illuminate\Support\Facades\DB;

class PackageService
{
    public function listPackagesForManager($managerId)
    {
        $packages = Package::whereHas('branch', fn($q) => $q->where('manager_id', $managerId))
                   ->with(['branch', 'occasionTypes', 'categories', 'extraServices'])
                   ->get();

        return response()->json(['packages' => $packages]);
    }

    public function createPackage(array $data, $managerId)
    {
        $branch = Branch::where('id', $data['branch_id'])
            ->where('manager_id', $managerId)
            ->first();

        if (!$branch) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $package = Package::create($data);

        $package->categories()->sync($data['category_ids'] ?? []);
        $package->occasionTypes()->sync($data['occasion_type_ids'] ?? []);
        $package->extraServices()->sync($data['branch_service_type_ids'] ?? []);

        return response()->json([
            'message' => 'Package created',
            'package' => $package->load('branch', 'occasionTypes', 'categories', 'extraServices')
        ], 201);
    }

    public function getPackageWithDetails($id, $managerId)
    {
        $package = Package::where('id', $id)
            ->whereHas('branch', fn($q) => $q->where('manager_id', $managerId))
            ->with(['branch', 'occasionTypes', 'categories', 'extraServices', 'items.foodItem', 'extras'])
            ->withCount('feedbacks as review_count')
            ->withAvg('feedbacks as average_rating', 'score')
            ->first();

        if (!$package) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $package->average_rating = number_format($package->average_rating ?? 0, 1);

        return response()->json(['package' => $package]);
    }

    public function updatePackage($id, array $data, $managerId)
    {
        $package = Package::where('id', $id)
            ->whereHas('branch', fn($q) => $q->where('manager_id', $managerId))
            ->firstOrFail();

        if (isset($data['branch_id'])) {
            $branch = Branch::where('id', $data['branch_id'])
                ->where('manager_id', $managerId)
                ->first();

            if (!$branch) {
                return response()->json(['error' => 'Unauthorized branch'], 403);
            }
        }

        $package->update($data);
        $package->categories()->sync($data['category_ids'] ?? []);
        $package->occasionTypes()->sync($data['occasion_type_ids'] ?? []);
        $package->extraServices()->sync($data['branch_service_type_ids'] ?? []);

        return response()->json(['message' => 'Updated', 'package' => $package->fresh()]);
    }

    public function deletePackage($id, $managerId)
    {
        $package = Package::where('id', $id)
            ->whereHas('branch', fn($q) => $q->where('manager_id', $managerId))
            ->firstOrFail();

        $package->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function addItemsToPackage(array $data)
    {
        foreach ($data['items'] as $item) {
            PackageItem::updateOrCreate(
                [
                    'package_id' => $data['package_id'],
                    'food_item_id' => $item['food_item_id']
                ],
                [
                    'quantity' => $item['quantity'],
                    'is_optional' => $item['is_optional'] ?? false,
                ]
            );
        }

        return response()->json(['message' => 'Items added']);
    }

    public function addExtrasToPackage(array $data)
    {
        foreach ($data['extras'] as $extra) {
            PackageExtra::create([
                'package_id' => $data['package_id'],
                'type' => $extra['type'] ?? null,
                'food_item_id' => $extra['food_item_id'] ?? null,
                'branch_service_type_id' => $extra['branch_service_type_id'] ?? null,
                'name' => $extra['name'] ?? 'Unnamed Extra',
                'price' => $extra['price'] ?? 0,
                'is_optional' => $extra['is_optional'] ?? true,
            ]);
        }

        return response()->json(['message' => 'Extras added']);
    }

    public function getPackageItems($packageId)
    {
        $items = PackageItem::with('foodItem')->where('package_id', $packageId)->get();
        return response()->json(['items' => $items]);
    }

    public function getPackageExtras($packageId)
    {
        $extras = PackageExtra::where('package_id', $packageId)->get();
        return response()->json(['extras' => $extras]);
    }
}
