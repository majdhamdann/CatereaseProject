<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardBranchService
{
    public function getBranchProfit($branch_id)
    {
        $branch = Branch::find($branch_id);

        if (!$branch || Auth()->user()->id != $branch->manager_id) {
           abort(403, 'Unauthorized access'); 
         }
        return Order::where('branch_id', $branch_id)
            ->where('status', 'delivered')
            ->sum('total_price');
    }
       public function getMyBranch()
   {
         $user = auth()->user();

         $branch = \App\Models\Branch::where('manager_id', $user->id)->first();

         if (!$branch) {
            return response()->json(['message' => 'No branch found for this manager'], 404);
          }

         return response()->json([
           'branch' => $branch
        ]);
    }

    public function getLastDeliveredOrdersWithRatings($branch_id)
    {
        $branch = Branch::find($branch_id);

        if (!$branch || Auth()->user()->id != $branch->manager_id) {
           abort(403, 'Unauthorized access'); 
         }
        $orders = Order::where('branch_id', $branch_id)
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $results = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $food = $detail->foodItem;

                $feedbacks = $food->feedbacks()->where('type', 'rating')->get();

                $average = round($feedbacks->avg('score') ?? 0, 1);
                $count = $feedbacks->count();

                $results[] = [
                    'order_id' => $order->id,
                    'dishName' => $food->name,
                    'dishImage' => $food->image_url,
                    'dishRate' => $average,
                    'number_of_ratings' => $count,
                    'order_cost' => $order->total_price,
                ];
            }
        }

        return $results;
    }

    public function getMonthlyDeliveredStats($branch_id)
    {
        $branch = Branch::find($branch_id);

        if (!$branch || Auth()->user()->id != $branch->Manager_id) {
           abort(403, 'Unauthorized access'); 
         }
        return Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('branch_id', $branch_id)
            ->where('status', 'delivered')
            ->whereYear('created_at', now()->year)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();
    }

    public function getOrderStatusCounts($branch_id)
    {
        $branch = Branch::find($branch_id);

        if (!$branch || Auth()->user()->id != $branch->manager_id) {
           abort(403, 'Unauthorized access'); 
         }
        $statuses = ['pending', 'confirmed', 'preparing', 'delivered', 'cancelled'];
        $response = [];

        foreach ($statuses as $status) {
            $response['order_' . $status] = Order::where('branch_id', $branch_id)
                ->where('status', $status)
                ->count();
        }

        return $response;
    }

    public function getMonthlyOrderStatusBreakdown($branch_id)
    {
        $branch = Branch::find($branch_id);

        if (!$branch || Auth()->user()->id != $branch->manager_id) {
           abort(403, 'Unauthorized access'); 
         }
        return Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw("COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count"),
                DB::raw("COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count")
            )
            ->where('branch_id', $branch_id)
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'), 'desc')
            ->orderBy(DB::raw('MONTH(created_at)'), 'desc')
            ->get();
    }

    public function getDeliveredItemsCategoryStats($branch_id)
    {
        $branch = Branch::find($branch_id);

        if (!$branch || Auth()->user()->id != $branch->manager_id) {
           abort(403, 'Unauthorized access'); 
         }
        $orders = Order::with(['orderDetails.foodItem.category'])
            ->where('status', 'delivered')
            ->where('branch_id', $branch_id)
            ->get();

        $totalDeliveredItems = 0;
        $categoryCounts = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $categoryName = $detail->foodItem->category->name ?? 'غير معروف';

                $categoryCounts[$categoryName] = ($categoryCounts[$categoryName] ?? 0) + 1;
                $totalDeliveredItems++;
            }
        }

        $results = [];
        foreach ($categoryCounts as $name => $count) {
            $percentage = $totalDeliveredItems > 0 ? round(($count / $totalDeliveredItems) * 100, 1) : 0;

            $results[] = [
                'category' => $name,
                'count' => $count,
                'percentage' => $percentage . '%',
            ];
        }

        return [
            'total_delivered_items' => $totalDeliveredItems,
            'category_distribution' => $results,
        ];
    }

    public function getPopularFoodCategories($branch_id)
    {
        $branch = Branch::find($branch_id);

        if (!$branch || Auth()->user()->id != $branch->manager_id) {
           abort(403, 'Unauthorized access'); 
         }
        $orders = Order::with(['orderDetails.foodItem.category'])
            ->where('branch_id', $branch_id)
            ->get();

        $categoryUserMap = [];

        foreach ($orders as $order) {
            $userId = $order->user_id;

            foreach ($order->orderDetails as $detail) {
                $categoryName = $detail->foodItem->category->name ?? 'غير معروف';
                $categoryUserMap[$categoryName][$userId] = true;
            }
        }

        $result = [];
        foreach ($categoryUserMap as $category => $users) {
            $result[] = [
                'name' => $category,
                'user_count' => count($users)
            ];
        }

        usort($result, fn($a, $b) => $b['user_count'] <=> $a['user_count']);

        return $result;
    }
}
