<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
   public function index()
    {
        $restaurant=Restaurant::all();
        return response()->json( $restaurant);
    }
       public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        'photo' => 'nullable|string',
        'owner_id' => 'required|exists:users,id',
    ]);

    $restaurant = Restaurant::create([
        'name' => $request->name,
        'description' => $request->description,
        'photo' => $request->photo, 
        'owner_id' => $request->owner_id,
        'created_at' => now(),
    ]);

    return response()->json($restaurant, 201);
}

    public function show($id)
{
    $restaurant = Restaurant::with('owner')->findOrFail($id);
    return response()->json($restaurant);
}  
  public function update(Request $request, $id)
{
    $restaurant = Restaurant::findOrFail($id);

    $request->validate([
        'name' => 'sometimes|string',
        'description' => 'sometimes|string',
        'photo' => 'sometimes|string', 
        'is_active' => 'sometimes|boolean',
    ]);

    $restaurant->update($request->only(['name', 'description', 'is_active', 'photo']));

    return response()->json($restaurant);
}
     public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully']);
    }
}
