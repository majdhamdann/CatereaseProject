<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Package;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class BranchStatisticsController extends Controller
{

  
public function getOrdersCountbranches()
{
    $owner = Auth::user();

    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    $branches = Branch::where('restaurant_id', $restaurant->id)->get();

    $stats = [];

    foreach ($branches as $branch) {
        $ordersData = Order::selectRaw('COUNT(*) as orders_count, SUM(total_price) as revenue')
            ->where('branch_id', $branch->id)
            ->where('status', 'delivered')
            ->first();
        $averageRating=$branch->feedbacks()->avg('score');
        

        $stats[] = [
            'branch_id' => $branch->id,
            'branch_name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
            'average_rating' => round($averageRating, 2),
            'total_orders' => $ordersData->orders_count ?? 0,
            'total_revenue' => (float) ($ordersData->revenue ?? 0),
        ];
    }

    return response()->json([
        'restaurant' => $restaurant->name,
        'branches' => $stats,
    ]);
}
public function getStatistics()
{
    $owner = Auth::user();

    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    $branches = Branch::where('restaurant_id', $restaurant->id)->get();

    $stats = [];

    foreach ($branches as $branch) {
        $monthlyOrders = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('branch_id', $branch->id)
            ->where('status', 'delivered')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('count', 'month');

        $totalRevenue = Order::where('branch_id', $branch->id)
            ->where('status', 'delivered')
            ->sum('total_price');

        $packageStats = OrderDetail::with(['package', 'order'])
    ->whereHas('order', function($query) use ($branch) {
        $query->where('branch_id', $branch->id)
              ->where('status', 'delivered');
    })
    ->select(
        'package_id',
        DB::raw('SUM(quantity) as total_orders'),
        DB::raw('SUM(quantity * unit_price) as total_revenue')
    )
    ->groupBy('package_id')
    ->get()
    ->map(function($item) {
        return [
            'name' => $item->package->name ?? 'غير معروف',
            'total_orders' => $item->total_orders,
            'total_revenue' => $item->total_revenue
        ];
    });


    


        $stats[] = [
            'branch_name' => $branch->location_note,
            'monthly_orders' => $monthlyOrders,
            'total_revenue' => $totalRevenue,
            'package_stats' => $packageStats,
           // 'category_stats' => $categoryStats,
        ];
    }

    return response()->json([
        'restaurant' => $restaurant->name,
        'statistics' => $stats
    ]);
}
public function getBranchesRevenueByMonth()
{
    $owner = Auth::user();

    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    $branches = Branch::where('restaurant_id', $restaurant->id)->get();

    $stats = [];

    foreach ($branches as $branch) {
        $monthlyRevenues = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
            ->where('branch_id', $branch->id)
            ->where('status', 'delivered')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $formatted = [];
        foreach ($monthlyRevenues as $data) {
            $monthName = Carbon::create()->month($data->month)->locale('en')->isoFormat('MMMM');
            $formatted[$monthName] = (float) $data->revenue;
        }

        $stats[] = [
            'branch_name' => $branch->location_note ?? 'فرع غير مسمى',
            'monthly_revenue' => $formatted,
        ];
    }

    return response()->json([
        'restaurant' => $restaurant->name,
        'branches' => $stats
    ]);
}

public function getBranchPackageStats()
{
    $owner = auth()->user();

    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    $branches = Branch::where('restaurant_id', $restaurant->id)->get();

    $result = [];

    foreach ($branches as $branch) {
        $packageStats = OrderDetail::whereHas('order', function($query) use ($branch) {
                $query->where('branch_id', $branch->id)
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
                    'total_revenue' => $item->total_revenue
                ];
            });

        $result[] = [
            'branch_name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
            'packages' => $packageStats,
        ];
    }

    return response()->json([
        'restaurant' => $restaurant->name,
        'branches' => $result,
    ]);
}
public function getOwnerSummary()
{
    $owner = Auth::user();

    $restaurant = Restaurant::withCount('branches')
        ->with(['branches' => function($query) {
            $query->withAvg('feedbacks as average_rating', 'score')
                  ->withCount('feedbacks as ratings_count');
        }])
        ->where('owner_id', $owner->id)
        ->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    $ordersQuery = Order::whereIn('branch_id', $restaurant->branches->pluck('id'))
        ->where('status', 'delivered');

    $totalOrders = $ordersQuery->count();
    $totalRevenue = $ordersQuery->sum('total_price');

    $averageRating = $restaurant->branches->avg('average_rating');
    $totalRating = $restaurant->branches->sum('ratings_count');

    $branchesStats = $restaurant->branches->map(function($branch) {
        return [
            'branch_id' => $branch->id,
            'branch_name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
            'average_rating' => round($branch->average_rating, 2),
            'ratings_count' => $branch->ratings_count
        ];
    });

    return response()->json([
        'restaurant' => $restaurant->name,
        'branches_count' => $restaurant->branches_count,
        'total_orders' => $totalOrders,
        'total_revenue' => (float) $totalRevenue,
        'average_rating' => round($averageRating, 2),
        'total_rating' => $totalRating,
        'branches_stats' => $branchesStats
    ]);
}
public function getBranchStatistics($branchId)
{
    $owner = Auth::user();

    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    $branch = Branch::withCount([
            'orders as total_orders_count' => function($query) {
                $query->where('status', 'delivered');
            },
            'orders as monthly_orders_count' => function($query) {
                $query->where('status', 'delivered')
                      ->select(DB::raw('COUNT(*)'));
            }
        ])
           ->withSum([
            'orders as total_revenue' => function($query) {
                $query->where('status', 'delivered');
            }
    ], 'total_price')
        ->where('id', $branchId)
        ->where('restaurant_id', $restaurant->id)
        ->first();

    if (!$branch) {
        return response()->json(['message' => 'الفرع غير موجود أو لا يتبع لهذا المطعم'], 404);
    }

    $monthlyData = Order::selectRaw('MONTH(created_at) as month, 
                                   COUNT(*) as orders_count, 
                                   SUM(total_price) as revenue')
        ->where('branch_id', $branch->id)
        ->where('status', 'delivered')
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->get();

    $monthlyStats = [];
    foreach ($monthlyData as $data) {
        $monthName = Carbon::create()->month($data->month)->locale('en')->isoFormat('MMMM');
        $monthlyStats[$monthName] = [
            'orders_count' => $data->orders_count,
            'revenue' => (float) $data->revenue,
        ];
    }

    $averageRating = $branch->feedbacks()->avg('score');
    $totalRatings = $branch->feedbacks()->count();
    $packageStats = OrderDetail::whereHas('order', function($query) use ($branch) {
                $query->where('branch_id', $branch->id)
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
    return response()->json([
        'branch_id' => $branch->id,
        'branch_name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
        'total_orders' => $branch->total_orders_count ?? 0,
        'total_revenue' => (float) ($branch->total_revenue ?? 0),
        'monthly_stats' => $monthlyStats,
        'average_rating' => round($averageRating, 2),
        'total_ratings' => $totalRatings,
        'packageStats' => $packageStats
    ]);
}

  

}
