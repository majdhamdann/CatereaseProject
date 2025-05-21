<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
   
    //ارباح الطلبات
    public function getBranchProfit($branch_id)
   {
      $income = Order::where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->sum('total_price')
        ->get();


      return response()->json([
        'branch_id' => $branch_id,
        'income' => $income
      ]);
    }
    //الاخيرة
   public function getOrderBranch($branch_id)
   {
      $orders = Order::where('branch_id', $branch_id)
            ->where('status', 'delivered')
            ->orderBy('created_at', 'desc')
            ->take( 10)
            ->get();

         $results = [];

       foreach ($orders as $order) {
        foreach ($order->orderDetails as $detail) {
            $food = $detail->foodItem;

           $feedbacks = $food->feedbacks()
           ->where('type', 'rating')
           ->get();

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

     return response()->json($results);
    }

    public function getRestaurantMonthlyOrderStats($branch_id)
{
    $deliveredPerMonth = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
        ->where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->whereYear('created_at', now()->year)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy('month')
        ->get();

    $totalOrders = Order::where('branch_id', $branch_id)->count('id');

    return response()->json([
        'delivered_orders_per_month' => $deliveredPerMonth,
        'total_orders' => $totalOrders
    ]);
}





    ////////////////////////////////////////////////////////////////////////////////////////
    //إرجاع تقييمات لطبق معين
    public function getFoodItemFeedback($food_item_id)
    {
       $feedbackType = FeedbackType::where('target_type', 'food_item')
                   ->where('target_ref_id', $food_item_id)
                   ->first();

       if (!$feedbackType) {
            return response()->json(['message' => 'No feedback available'], 404);
        }

       $feedbacks = $feedbackType->feedbacks()->with('user')->get();

       return response()->json($feedbacks);
    }
//تقييم طبق (food_item)
       public function submitFoodItemFeedback(Request $request, $food_item_id)
   {
        $request->validate([
        'type' => 'required|in:rating,complaint',
        'score' => 'nullable|numeric|min:0|max:5',
        'content' => 'nullable|string',
    ]);

    $feedbackType = FeedbackType::firstOrCreate([
        'target_type' => 'food_item',
        'target_ref_id' => $food_item_id,
    ]);

    $feedback = new Feedback();
    $feedback->FeedbackType_id = $feedbackType->id;
    $feedback->user_id = auth()->id();
    $feedback->type = $request->type;
    $feedback->score = $request->score;
    $feedback->content = $request->content;
    $feedback->save();

    return response()->json([
        'message' => 'Feedback submitted successfully',
        'data' => $feedback
    ]);
}



}
