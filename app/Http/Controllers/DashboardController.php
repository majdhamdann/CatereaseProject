<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardBranchService;
use Carbon\Carbon;

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
        return response()->json($this->analytics->getPopularPackageCategories($branch_id));
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
    
 public function getPopularPackagesThisWeek($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $oneWeekAgo = Carbon::now()->subDays(7);

    $orders = Order::with('orderDetails.package')
        ->where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->where('created_at', '>=', $oneWeekAgo)
        ->get();

    $packageCounts = [];

    foreach ($orders as $order) {
        foreach ($order->orderDetails as $detail) {
            $package = $detail->package;
            if ($package) {
                $packageName = $package->name;
                $packageCounts[$packageName] = ($packageCounts[$packageName] ?? 0) + 1;
            }
        }
    }

    // تحويل النتائج إلى ترتيب تنازلي حسب عدد الطلبات
    $result = [];
    foreach ($packageCounts as $package => $count) {
        $result[] = [
            'package' => $package,
            'order_count' => $count,
        ];
    }

    usort($result, fn($a, $b) => $b['order_count'] <=> $a['order_count']);

    return $result;
}

  public function getBestSellerPackages($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $orders = Order::with('orderDetails.package')
        ->where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->get();

    $packageCounts = [];

    foreach ($orders as $order) {
        foreach ($order->orderDetails as $detail) {
            $package = $detail->package;
            if ($package) {
                $packageName = $package->name;
                $packageCounts[$packageName] = ($packageCounts[$packageName] ?? 0) + 1;
            }
        }
    }

    $result = [];
    foreach ($packageCounts as $package => $count) {
        $result[] = [
            'package' => $package,
            'order_count' => $count,
        ];
    }
    usort($result, fn($a, $b) => $b['order_count'] <=> $a['order_count']);

    return $result;
}
public function getBranchCustomers($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    // جلب الطلبات مع بيانات المستخدم
    $orders = Order::with('user')
        ->where('branch_id', $branch_id)
        ->get();

    $customers = [];

    foreach ($orders as $order) {
        $user = $order->user;
        if ($user) {
            if (!isset($customers[$user->id])) {
                // أول مرة نضيف المستخدم
                $customers[$user->id] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'orders' => [],
                    'total_orders' => 0,
                    'customer_since' => $user->created_at->format('Y-m-d'),  
                ];
            }

            $customers[$user->id]['orders'][] = [
                'order_id' => $order->id,
                'order_date' => $order->created_at->format('Y-m-d'),
                'order_status' => $order->status,
                'order_total' => $order->total_price,
            ];

            $customers[$user->id]['total_orders']++;
        }
    }

    return array_values($customers);
}





}
