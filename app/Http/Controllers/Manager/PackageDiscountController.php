<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\PackageDiscount;
use Illuminate\Http\Request;

class PackageDiscountController extends Controller
{
    public function index()
    {
        $discounts = PackageDiscount::with('package')->get()->map(function ($discount) {
           $discount->value = number_format($discount->value, 2) . '%';

            return $discount;
        });

        return response()->json([
            'status' => true,
            'discounts' => $discounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'value' => 'required|numeric|min:0',
            'description' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
        ]);

        $discount = PackageDiscount::create($validated);

        $discount->value = $discount->value . '%';

        return response()->json([
            'status' => true,
            'message' => 'Discount added successfully.',
            'discount' => $discount,
        ], 201);
    }

    public function destroy($id)
    {
        $discount = PackageDiscount::findOrFail($id);
        $discount->delete();

        $discount->value = $discount->value . '%';

        return response()->json([
            'status' => true,
            'message' => 'Discount deleted successfully.',
            'discount' => $discount,
        ]);
    }
}
