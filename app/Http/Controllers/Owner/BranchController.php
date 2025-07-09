<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BranchController extends Controller
{

   public function showRestaurantDetails()
{
    $user = Auth::user();
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

    $restaurant = $user->restaurant;

    $branches = $restaurant->branches()->get();

    return response()->json([
        'branches' => $branches
    ]);
}


    private function ensureIsRestaurantOwner($user)
    {
        if ( !$user->restaurant) {
            abort(403, 'Only restaurant owners can perform this action');
        }
    }

    private function handleBase64Image($base64Image, $path = 'branch_photos')
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $image = substr($base64Image, strpos($base64Image, ',') + 1);
            $image = base64_decode($image);
            $extension = strtolower($type[1]); 

            $filename = $path . '/' . uniqid() . '.' . $extension;
            Storage::disk('public')->put($filename, $image);

            return $filename;
        }

        return null;
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $restaurant = $user->restaurant;

        $data = $request->validate([
            'description' => 'nullable|string',
            'photo' => 'nullable|string', // base64 string
            'location_note' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'city_id' => 'nullable|exists:cities,id',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        if (isset($data['photo'])) {
            $data['photo'] = $this->handleBase64Image($data['photo']);
        }

        $data['restaurant_id'] = $restaurant->id;

        $branch = Branch::create($data);

        return response()->json(['message' => 'Branch created', 'branch' => $branch]);
    }
  public function addCategoriesToBranch(Request $request, $branchId)
{
    $user = Auth::user();
    $this->ensureIsRestaurantOwner($user);

    $branch = Branch::where('id', $branchId)
        ->where('restaurant_id', $user->restaurant->id)
        ->first();

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

        $branch = Branch::where('id', $id)
            ->where('restaurant_id', $user->restaurant->id)
            ->first();

        if (!$branch) {
            return response()->json(['error' => 'Branch not found or unauthorized'], 404);
        }

        return response()->json(['branch' => $branch]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $branch = Branch::where('id', $id)
            ->where('restaurant_id', $user->restaurant->id)
            ->firstOrFail();

        $data = $request->validate([
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'location_note' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|string', // base64 string
        ]);

        if (isset($data['photo'])) {
            $data['photo'] = $this->handleBase64Image($data['photo']);
        }

        $branch->update($data);

        return response()->json(['message' => 'Branch updated', 'branch' => $branch]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $this->ensureIsRestaurantOwner($user);

        $branch = Branch::where('id', $id)
            ->where('restaurant_id', $user->restaurant->id)
            ->firstOrFail();

        $branch->delete();

        return response()->json(['message' => 'Branch deleted']);
    }
}
