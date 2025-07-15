<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class branchtatisticsontroller extends Controller
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
        $monthlyData = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as orders_count, SUM(total_price) as revenue')
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

        $stats[] = [
            'branch_name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
            'monthly_stats' => $monthlyStats,
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

        $packageStats = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('packages', 'order_details.package_id', '=', 'packages.id')
            ->select(
                'packages.name',
                DB::raw('SUM(order_details.quantity) as total_orders'),
                DB::raw('SUM(order_details.quantity * order_details.unit_price) as total_revenue')
            )
            ->where('orders.branch_id', $branch->id)
            ->where('orders.status', 'delivered')
            ->groupBy('packages.name')
            ->get();

    


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

public function getBranchFoodItemStats()
{
    $owner = auth()->user();

    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    $branches = Branch::where('restaurant_id', $restaurant->id)->get();

    $result = [];

    foreach ($branches as $branch) {
        $foodStats = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('packages', 'order_details.package_id', '=', 'packages.id')
            ->join('package_items', 'packages.id', '=', 'package_items.package_id')
            ->join('food_items', 'package_items.food_item_id', '=', 'food_items.id')
            ->where('orders.branch_id', $branch->id)
            ->where('orders.status', 'delivered')
            ->select(
                'food_items.id as food_item_id',
                'food_items.name as food_item_name',
                DB::raw('SUM(order_details.quantity * package_items.quantity) as total_orders'),
                DB::raw('SUM(order_details.unit_price * package_items.quantity) as total_revenue')
            )
            ->groupBy('food_items.id', 'food_items.name')
            ->get();

        $result[] = [
            'branch_name' => $branch->location_note ?? $branch->description ?? 'بدون اسم',
            'food_items' => $foodStats,
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

    // جلب المطعم التابع للمالك
    $restaurant = Restaurant::where('owner_id', $owner->id)->first();

    if (!$restaurant) {
        return response()->json(['message' => 'لا يوجد مطعم لهذا المالك'], 404);
    }

    // عدد الفروع التابعة للمطعم
    $branchesCount = Branch::where('restaurant_id', $restaurant->id)->count();

    // جميع طلبات الفروع التابعة للمطعم
    $ordersQuery = Order::whereIn('branch_id', function ($query) use ($restaurant) {
        $query->select('id')
            ->from('branches')
            ->where('restaurant_id', $restaurant->id);
    })->where('status', 'delivered');

    // إجمالي عدد الطلبات
    $totalOrders = $ordersQuery->count();

    // إجمالي الإيرادات من الطلبات
    $totalRevenue = $ordersQuery->sum('total_price');

    return response()->json([
        'restaurant' => $restaurant->name,
        'branches_count' => $branchesCount,
        'total_orders' => $totalOrders,
        'total_revenue' => (float) $totalRevenue,
    ]);
}




}
