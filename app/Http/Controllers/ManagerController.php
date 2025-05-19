<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
   
    public function getBranchProfit($branch_id)
{
    $income = DB::table('orders')
        ->where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->sum('total_price');


    return response()->json([
        'branch_id' => $branch_id,
        'income' => $income
    ]);
    }
    public function getLatestDeliveredOrdersForBranch($branch_id)
{
    // اجلب آخر 10 طلبات مكتملة من هذا الفرع
    $orders = Order::where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    $results = [];

    foreach ($orders as $order) {
        foreach ($order->orderDetails as $detail) {
            $food = $detail->foodItem;

            $ratingInfo = $food->feedbacks()
                ->where('type', 'rating')
                ->selectRaw('AVG(score) as average_rating, COUNT(*) as rating_count')
                ->first();

            $results[] = [
                'order_id' => $order->Order_id,
                'dishName' => $food->name,
                'dishImage' => $food->image_url,
                'dishRate' => round($ratingInfo->average_rating ?? 0, 1),
                'number_of_ratings' => $ratingInfo->rating_count ?? 0,
                'order_cost' => $order->total_price,
            ];
        }
    }

    return response()->json($results);
}


}
