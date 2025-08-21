<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsAdminController extends Controller
{
      public function index()
    {
        $totalRevenue = Order::sum('total_price');

        $totalOrders = Order::count();

        $totalRestaurants = Restaurant::count();
        $activeRestaurants = Restaurant::where('is_active', true)->count();
        $averageSatisfaction = Feedback::where('type','rating')->avg('score');
        $restaurants = Restaurant::with(['branches.orders' => function ($query) {
            $query->select('id', 'branch_id', 'total_price');
        }])->get()->map(function ($restaurant) {
            $restaurantRevenue = $restaurant->branches->flatMap->orders->sum('total_price');
            $restaurantOrders  = $restaurant->branches->flatMap->orders->count();

            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'total_revenue' => $restaurantRevenue,
                'total_orders'  => $restaurantOrders,
                'branches_count' => $restaurant->branches->count(),
            ];
        });

        return response()->json([
            'status' => true,
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'total_restaurants' => $totalRestaurants,
            'activeRestaurants' => $activeRestaurants,
            'averageSatisfaction' => round($averageSatisfaction, 2),
            'restaurants' => $restaurants
        ]);
    }
     public function restaurantsSummary()
{
    $restaurants = Restaurant::with('branches.orders')->get()->map(function ($restaurant) {
        $orders = $restaurant->branches->flatMap->orders;

        return [
            'RestaurantName' => $restaurant->name,
            'TotalOrders' => $orders->count(),
            'TotalRevenue' => $orders->sum('total_price'),
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $restaurants
    ]);
}

   public function popularPackages()
{
    $packages = OrderDetail::select(
            'package_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(quantity * unit_price) as total_revenue'),
            DB::raw('COUNT(order_id) as total_orders')
        )
        ->groupBy('package_id')
        ->with('package') 
        ->orderByDesc('total_orders')
        ->get()
        ->map(function ($item) {
            return [
                'PackageName'   => $item->package->name ?? 'غير معروف',
                'TotalOrders'   => $item->total_orders,
                'TotalQuantity' => $item->total_quantity,
                'TotalRevenue'  => $item->total_revenue,
            ];
        });

    return response()->json([
        'status' => true,
        'data' => $packages
    ]);
}

    public function restaurantStats($restaurantId)
{
    $restaurant = Restaurant::with('branches.orders')->findOrFail($restaurantId);

    // جمع الطلبات من كل الفروع
    $orders = $restaurant->branches->flatMap->orders;

    return response()->json([
        'status' => true,
        'data' => [
            'RestaurantId'   => $restaurant->id,
            'RestaurantName' => $restaurant->name,
            'RestaurantPhoto'=> $restaurant->photo,
            'TotalOrders'    => $orders->count(),
            'TotalRevenue'   => $orders->sum('total_price'),
        ]
    ]);
}


}
