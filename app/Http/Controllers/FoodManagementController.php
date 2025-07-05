<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFoodItemRequest;
use App\Http\Requests\updateFoodItemRequest;
use App\Models\FoodItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $user = Auth::user();

    if ($user->role->name === 'manager') {
        $managedBranch = $user->managedBranch;

        if (!$managedBranch) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $items = FoodItem::where('branch_id', $managedBranch->id)->get();
    } else {
        $items = FoodItem::all();
    }

    return response()->json($items);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFoodItemRequest $request)
    {
        $user = Auth::user();

        if ($user->role->name === 'manager' ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $validated = $request->validated();

        $managedBranch = $user->managedBranch;
         if (!$managedBranch) {
        return response()->json(['error' => 'You do not manage any branch'], 403);
        }
        $validated['branch_id'] = $managedBranch->id;

        $item = FoodItem::create($validated);

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $user = Auth::user();
          $item = FoodItem::findOrFail($id);
          return response()->json($item);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(updateFoodItemRequest $request, string $id)
    {
        $user = Auth::user();
        $item = FoodItem::findOrFail($id);

        $branch = $item->branch;

    if ($user->role->name === 'manager' && $branch->Manager_id !== $user->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $validated = $request->validated();

    $item->update($validated);

    return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $user = Auth::user();
        $item = FoodItem::findOrFail($id);

        if ($user->role->name === 'Manager' && $item->branch_id !== $user->branch_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $item->delete();

        return response()->json(['message' => 'Food item deleted']);
    
    }
}
