<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OccasionType;
use Illuminate\Http\Request;

class OccasionTypeController extends Controller
{
    public function index()
    {
        $occasions = OccasionType::all();
        return response()->json($occasions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:occasion_types,name',
            'description' => 'nullable|string',
        ]);

        $occasion = OccasionType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return response()->json(['message' => 'Occasion created', 'data' => $occasion]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required|string|unique:occasion_types,name,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $occasion = OccasionType::findOrFail($id);
        $occasion->update($request->only(['name', 'description', 'is_active']));

        return response()->json(['message' => 'Occasion updated', 'data' => $occasion]);
    }

    public function destroy($id)
    {
        $occasion = OccasionType::findOrFail($id);

        if ($occasion->packages()->count() > 0) {
            return response()->json(['message' => 'Cannot delete, occasion is in use'], 400);
        }

        $occasion->delete();

        return response()->json(['message' => 'Occasion deleted']);
    }
}
