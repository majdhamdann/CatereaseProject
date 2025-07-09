<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
   public function index()
    {
        return response()->json(Restaurant::all());
    }
       public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|image',
            'owner_id' => 'required|exists:users,id',
            
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('restaurant_photos', 'public')
            : null;

        $restaurant = Restaurant::create([
            'name' => $request->name,
            'description' => $request->description,
            'photo' => $photoPath,
            'owner_id' => $request->owner_id,
            'created_at' => now(),
        ]);

        return response()->json($restaurant, 201);
    }
     public function show($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return response()->json($restaurant);
    }
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'photo' => 'sometimes|image',
        ]);

        if ($request->hasFile('photo')) {
            $restaurant->photo = $request->file('photo')->store('restaurant_photos', 'public');
        }

        $restaurant->update($request->only(['name', 'description']));
        return response()->json($restaurant);
    }
     public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully']);
    }
}
