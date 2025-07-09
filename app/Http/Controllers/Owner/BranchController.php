<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
   public function store(Request $request)
{
    $user = Auth::user();

    $restaurant = $user->restaurant;
    if (!$restaurant) {
        return response()->json(['error' => 'You do not own a restaurant'], 403);
    }

    $data = $request->validate([
        'description' => 'nullable|string',
        'photo' => 'nullable|image',
        'location_note' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'city_id' => 'nullable|exists:cities,id',
        'manager_id' => 'nullable|exists:users,id',
    ]);

    if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('branch_photos', 'public');
    }

    $data['restaurant_id'] = $restaurant->id;

    $branch = \App\Models\Branch::create($data);

    return response()->json(['message' => 'Branch created', 'branch' => $branch]);
}
public function show($id)
{
    $user = Auth::user();
    $restaurant = $user->restaurant;

    $branch = \App\Models\Branch::where('id', $id)
        ->where('restaurant_id', $restaurant->id)
        ->first();

    if (!$branch) {
        return response()->json(['error' => 'Branch not found or unauthorized access'], 404);
    }

    return response()->json(['branch' => $branch]);
}

public function update(Request $request, $id)
{
    $user = Auth::user();
    $restaurant = $user->restaurant;

    $branch = \App\Models\Branch::where('id', $id)
        ->where('restaurant_id', $restaurant->id)
        ->firstOrFail();

    $data = $request->validate([
        'description' => 'nullable|string',
        'manager_id' => 'nullable|exists:users,id',
        'location_note' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    $branch->update($data);

    return response()->json(['message' => 'Branch updated', 'branch' => $branch]);
}
public function destroy($id)
{
    $user = Auth::user();
    $restaurant = $user->restaurant;

    $branch = \App\Models\Branch::where('id', $id)
        ->where('restaurant_id', $restaurant->id)
        ->firstOrFail();

    $branch->delete();

    return response()->json(['message' => 'Branch deleted']);
}

}
