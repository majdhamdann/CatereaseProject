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

    $photoPath = null;

    if ($request->filled('photo')) {
        $photoData = $request->photo;

        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
            $extension = strtolower($type[1]); // jpg, png, gif...

            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                return response()->json(['message' => 'Invalid image format'], 422);
            }

            $photoData = base64_decode($photoData);

            if ($photoData === false) {
                return response()->json(['message' => 'Base64 decode failed'], 422);
            }

            $fileName = Str::uuid() . '.' . $extension;
            $path = 'restaurant_photos/' . $fileName;

            Storage::disk('public')->put($path, $photoData);

            $photoPath = $path;
        } else {
            return response()->json(['message' => 'Invalid base64 image format'], 422);
        }
    }

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
            'is_active' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('photo')) {
            $restaurant->photo = $request->file('photo')->store('restaurant_photos', 'public');
        }
        elseif ($request->filled('photo') && is_string($request->photo)) {
        $photoData = $request->photo;

        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
            $extension = strtolower($type[1]);

            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return response()->json(['message' => 'Invalid image format'], 422);
            }

            $photoData = base64_decode($photoData);
            if ($photoData === false) {
                return response()->json(['message' => 'Base64 decode failed'], 422);
            }

            $fileName = Str::uuid() . '.' . $extension;
            $path = 'restaurant_photos/' . $fileName;
            Storage::disk('public')->put($path, $photoData);

            $restaurant->photo = $path;
        } else {
            return response()->json(['message' => 'Invalid base64 image format'], 422);
        }
    }

        $restaurant->update($request->only(['name', 'description','is_active','photo' ]));
        return response()->json($restaurant);
    }
     public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully']);
    }
}
