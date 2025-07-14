<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageDiscount;
use Auth;
use Illuminate\Http\Request;

class PackageDiscountController extends Controller
{
      public function index()
    {
        $user = Auth::user();

        $packages = Package::whereHas('discounts')
           ->whereHas('branch', function ($query) use ($user) {
               $query->where('manager_id', $user->id);
           })
          ->with([
            'discounts', 
            'categories'    
        ])
           ->get();

       $packages->each(function ($package) {
          $package->discounts->each(function ($discount) {
              $discount->value = number_format($discount->value, 2) . '%';
           });
       });

      return response()->json([
         'status' => true,
         'packages' => $packages,
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
