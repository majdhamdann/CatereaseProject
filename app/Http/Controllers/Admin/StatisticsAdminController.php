<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Package;
use App\Models\Restaurant;
use Carbon\Carbon;
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
    $restaurant = Restaurant::with('branches')
        ->where('id', $restaurantId)
        ->first();

    if (!$restaurant) {
        return response()->json(['message' => 'المطعم غير موجود أو لا ينتمي لهذا المالك'], 404);
    }

    $branchIds = $restaurant->branches->pluck('id');

    if ($branchIds->isEmpty()) {
        return response()->json([
            'restaurant_id' => $restaurant->id,
            'restaurant_name' => $restaurant->name,
            'restaurant_photo' => $restaurant->photo,
            'total_orders' => 0,
            'total_revenue' => 0,
            'monthly_stats' => [],
            'average_rating' => 0,
            'total_ratings' => 0,
            'packageStats' => [],
            'branches_stats' => [],
            'branch_ratings' => [],
            'package_ratings' => []
        ]);
    }

    $ordersStats = Order::selectRaw('
            COUNT(*) as total_orders_count,
            SUM(total_price) as total_revenue,
            MONTH(created_at) as month
        ')
        ->whereIn('branch_id', $branchIds)
        ->where('status', 'delivered')
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->get();

    $monthlyStats = [];
    $totalOrders = 0;
    $totalRevenue = 0;

    foreach ($ordersStats as $data) {
        $monthName = Carbon::create()->month($data->month)->locale('ar')->isoFormat('MMMM');
        $monthlyStats[$monthName] = [
            'orders_count' => $data->total_orders_count,
            'revenue' => (float) $data->total_revenue,
        ];
        
        $totalOrders += $data->total_orders_count;
        $totalRevenue += $data->total_revenue;
    }

    $branchRatings = DB::table('feedback')
        ->join('feedback_types', 'feedback.FeedbackType_id', '=', 'feedback_types.id')
        ->where('feedback_types.target_type', 'branch')
        ->whereIn('feedback_types.target_ref_id', $branchIds)
        ->select(
            'feedback_types.target_ref_id as branch_id',
            DB::raw('COUNT(feedback.id) as total_ratings'),
            DB::raw('AVG(feedback.score) as average_rating')
        )
        ->groupBy('feedback_types.target_ref_id')
        ->get()
        ->map(function($item) {
            return [
                'branch_id' => $item->branch_id,
                'total_ratings' => $item->total_ratings,
                'average_rating' => round($item->average_rating, 2)
            ];
        });

    $packageIds = OrderDetail::whereHas('order', function($query) use ($branchIds) {
            $query->whereIn('branch_id', $branchIds)
                  ->where('status', 'delivered');
        })
        ->pluck('package_id')
        ->unique();

    $packageRatings = DB::table('feedback')
        ->join('feedback_types', 'feedback.FeedbackType_id', '=', 'feedback_types.id')
        ->where('feedback_types.target_type', 'package')
        ->whereIn('feedback_types.target_ref_id', $packageIds)
        ->select(
            'feedback_types.target_ref_id as package_id',
            DB::raw('COUNT(feedback.id) as total_ratings'),
            DB::raw('AVG(feedback.score) as average_rating')
        )
        ->groupBy('feedback_types.target_ref_id')
        ->get()
        ->map(function($item) use ($packageIds) {
            $package = Package::find($item->package_id);
            return [
                'package_id' => $item->package_id,
                'package_name' => $package->name ?? 'غير معروف',
                'total_ratings' => $item->total_ratings,
                'average_rating' => round($item->average_rating, 2),
                'categories' => $package ? $package->categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name
                    ];
                })->toArray() : []
            ];
        });

    // إحصائيات الباقات
    $packageStats = OrderDetail::whereHas('order', function($query) use ($branchIds) {
            $query->whereIn('branch_id', $branchIds)
                  ->where('status', 'delivered');
        })
        ->with('package')
        ->select(
            'package_id',
            DB::raw('SUM(quantity) as total_orders'),
            DB::raw('SUM(unit_price * quantity) as total_revenue')
        )
        ->groupBy('package_id')
        ->get()
        ->map(function($item) {
            return [
                'package_id' => $item->package_id,
                'package_name' => $item->package->name ?? 'غير معروف',
                'total_orders' => $item->total_orders,
                'total_revenue' => $item->total_revenue,
                'categories' => $item->package->categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name
                    ];
                })->toArray()
            ];
        });

    $branchesStats = Branch::whereIn('id', $branchIds)
        ->withCount([
            'orders as total_orders_count' => function($query) {
                $query->where('status', 'delivered');
            }
        ])
        ->withSum([
            'orders as total_revenue' => function($query) {
                $query->where('status', 'delivered');
            }
        ], 'total_price')
        ->get()
        ->map(function($branch) use ($branchRatings) {
            // البحث عن تقييمات الفرع
            $branchRating = $branchRatings->firstWhere('branch_id', $branch->id);
            
            return [
                'branch_id' => $branch->id,
                'branch_name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
                'total_orders' => $branch->total_orders_count ?? 0,
                'total_revenue' => (float) ($branch->total_revenue ?? 0),
                'average_rating' => $branchRating ? $branchRating['average_rating'] : 0,
                'total_ratings' => $branchRating ? $branchRating['total_ratings'] : 0
            ];
        });

    // الإحصائيات العامة للتقييمات
    $totalRatings = $branchRatings->sum('total_ratings') + $packageRatings->sum('total_ratings');
    $averageRating = ($branchRatings->avg('average_rating') + $packageRatings->avg('average_rating')) / 2;

    return response()->json([
        'restaurant_id' => $restaurant->id,
        'restaurant_name' => $restaurant->name,
        'restaurant_photo' => $restaurant->photo,
        'total_orders' => $totalOrders,
        'total_revenue' => (float) $totalRevenue,
        'monthly_stats' => $monthlyStats,
        'average_rating' => round($averageRating, 2),
        'total_ratings' => $totalRatings,
        'packageStats' => $packageStats,
        'branches_stats' => $branchesStats,
        'branch_ratings' => $branchRatings,
        'package_ratings' => $packageRatings
    ]);
}


}
