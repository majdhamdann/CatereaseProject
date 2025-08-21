<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Package;
use App\Models\PackageDiscount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;
use Carbon\Carbon;
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
            'categories',
            'feedbacks',
            'complaint'    
        ])
           ->get();

       $packages->each(function ($package) {
          $package->discounts->each(function ($discount) {
              $discount->value = number_format($discount->value, 2) . '%';
           });
       });
       $packages->each(function ($package) {
       $averageRating = $package->feedbacks->avg('score') ?? 0;
       $reviewsCount = $package->feedbacks->count();
       $package->average_rating = round($averageRating, 1);
       $package->reviewsCount =  $reviewsCount ;

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
      public function getDiscountedPackages()
{
    $manager = Auth::user();

    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بهذا المدير'], 404);
    }

    $now = Carbon::now();

    $packages = Package::where('branch_id', $branch->id)
        ->whereHas('discounts', function ($query) use ($now) {
            $query->where('is_active', true)
                  ->where('start_at', '<=', $now)
                  ->where('end_at', '>=', $now);
        })
        ->with(['discounts' => function ($query) use ($now) {
            $query->where('is_active', true)
                  ->where('start_at', '<=', $now)
                  ->where('end_at', '>=', $now);
        }, 'feedbacks','categories'])
        ->get();

    $data = $packages->map(function ($package) {
        $discount = $package->discounts->first();
        $reviewsCount = $package->feedbacks->count();

        $averageRating = $package->feedbacks->avg('rating'); 

        return [
            'id' => $package->id,
            'name' => $package->name,
            'photo' => $package->photo,
            'old_price' => $package->base_price,
            'discount_value' => $discount ? $discount->value . '%' : '0%',
            'reviews_count' => $reviewsCount,
            'average_rating' => round($averageRating, 1), 
            'category_ids' => $package->categories->pluck('id')->toArray(),

        ];
    });

    return response()->json($data);
}
public function getPackageDiscounts($packageId){
    $discount=PackageDiscount::where('package_id',$packageId)->get();
    return $discount;
}

}
