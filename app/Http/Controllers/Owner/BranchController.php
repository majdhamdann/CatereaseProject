<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    public function showRestaurantDetails()
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'You do not own a restaurant.'], 404);
        }

        return response()->json([
            'restaurant' => $restaurant
        ]);
    }

    public function index()
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branches = $role === 'Admin'
            ? Branch::all()
            : $user->restaurant->branches()->get();

        return response()->json([
            'branches' => $branches
        ]);
    }

    private function ensureIsRestaurantOwner($user)
    {
        $role = $user->role->name ?? null;

        if ($role !== 'Admin' && !$user->restaurant) {
            abort(403, 'Only restaurant owners or admins can perform this action');
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $validationRules = [
            'description' => 'nullable|string',
            'location_note' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
            'manager_id' => 'nullable|exists:users,id',
        ];

        if ($role === 'Admin') {
            $validationRules['restaurant_id'] = 'required|exists:restaurants,id';
        }

        $data = $request->validate($validationRules);

        if ($role !== 'Admin') {
            $data['restaurant_id'] = $user->restaurant->id;
        }

        $branch = Branch::create($data);

        return response()->json(['message' => 'Branch created', 'branch' => $branch]);
    }

    public function addCategoriesToBranch(Request $request, $branchId)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branch = $role === 'Admin'
            ? Branch::find($branchId)
            : Branch::where('id', $branchId)->where('restaurant_id', $user->restaurant->id)->first();

        if (!$branch) {
            return response()->json(['error' => 'Branch not found or unauthorized'], 404);
        }

        $data = $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $branch->categories()->syncWithoutDetaching($data['category_ids']);

        return response()->json([
            'message' => 'Categories linked to branch successfully',
            'branch' => $branch->load('categories')
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branch = $role === 'Admin'
            ? Branch::find($id)
            : Branch::where('id', $id)->with('packages')->where('restaurant_id', $user->restaurant->id)->first();

        if (!$branch) {
            return response()->json(['error' => 'Branch not found or unauthorized'], 404);
        }

        return response()->json(['branch' => $branch]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branch = $role === 'Admin'
            ? Branch::findOrFail($id)
            : Branch::where('id', $id)
                ->where('restaurant_id', $user->restaurant->id)
                ->firstOrFail();

        $rules = [
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'location_note' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ];

        if ($role === 'Admin') {
            $rules['restaurant_id'] = 'sometimes|exists:restaurants,id';
        }

        $data = $request->validate($rules);

        $branch->update($data);

        return response()->json(['message' => 'Branch updated', 'branch' => $branch]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $role = $user->role->name ?? null;

        $branch = $role === 'Admin'
            ? Branch::findOrFail($id)
            : Branch::where('id', $id)->where('restaurant_id', $user->restaurant->id)->firstOrFail();

        $branch->delete();

        return response()->json(['message' => 'Branch deleted']);
    }
      public function getOwnerBranchesWithPackages()
{
    $owner = auth()->user(); 

    $branches = Branch::with([
        'packages.categories',           
        'packages.extraServices.serviceType', 
        'packages.occasionTypes',        
        'branchServiceTypes.serviceType' ,
        'deliveryAreas.city'  
    ])
    ->whereHas('restaurant', function ($q) use ($owner) {
        $q->where('owner_id', $owner->id);
    })
    ->get();

    return response()->json([
        'status' => true,
        'data' => $branches
    ]);
}
public function getOwnerBranches()
{
    $owner = Auth::user();
    $this->ensureIsRestaurantOwner($owner);

    $branches = Branch::with(['restaurant.owner', 'city'])
        ->whereHas('restaurant', function($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
        ->get()
        ->map(function($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
                'image' => $branch->photo, 
                'ownerName' => $branch->restaurant->owner->name ?? 'غير معروف',
                'Manager' => $branch->manager->name ?? 'غير معروف',
                'city' => $branch->city->name ?? 'غير معروفة'
            ];
        });

    return response()->json([
        'status' => true,
        'branches' => $branches
    ]);
}
}
