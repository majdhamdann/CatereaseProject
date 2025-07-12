<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardBranchService;
class DashboardController extends Controller
{



    protected $analytics;

    public function __construct(DashboardBranchService $analytics)
    {
        $this->analytics = $analytics;
    }
    public function getMyBranch()
{
     $branch = $this->analytics->getMyBranch();

        return response()->json([
            'branch' => $branch
        ]);
}


    // أرباح الطلبات
    public function getBranchProfit($branch_id)
    {
         return $this->analytics->getBranchProfit($branch_id);
    }

    // آخر 10 طلبات مع تقييم الأطعمة
    public function getLatestDeliveredOrders($branch_id)
    {
        $data = $this->analytics->getLastDeliveredOrdersWithRatings($branch_id);
        return response()->json($data);
    }

    // عدد الطلبات لكل شهر وحساب الإجمالي
    public function getMonthlyDeliveredOrders($branch_id)
    {
        $delivered = $this->analytics->getMonthlyDeliveredStats($branch_id);
        $total = $this->analytics->getOrderStatusCounts($branch_id)['order_delivered'] ?? 0;

        return response()->json([
            'delivered_orders_per_month' => $delivered,
            'total_orders' => $total,
        ]);
    }

    // عدد الطلبات حسب الحالة
    public function getOrderStatusCounts($branch_id)
    {
        return response()->json($this->analytics->getOrderStatusCounts($branch_id));
    }

    // تفصيل الطلبات شهريًا حسب الحالة
    public function getMonthlyStatusBreakdown($branch_id)
    {
        return response()->json($this->analytics->getMonthlyOrderStatusBreakdown($branch_id));
    }

    // توزيع الطلبات حسب التصنيف مع النسبة
    public function getDeliveredCategoryStats($branch_id)
    {
        return response()->json($this->analytics->getDeliveredItemsCategoryStats($branch_id));
    }

    // أكثر أنواع الطعام طلبًا من حيث عدد المستخدمين
    public function getPopularCategoriesByUsers($branch_id)
    {
        return response()->json($this->analytics->getPopularFoodCategories($branch_id));
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
    $feedback->message = $request->message;
    $feedback->save();

    return response()->json([
        'message' => 'Feedback submitted successfully',
        'data' => $feedback
    ]);
}



}
