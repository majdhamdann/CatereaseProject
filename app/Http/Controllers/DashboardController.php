<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Feedback;
use App\Models\FeedbackType;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DashboardBranchService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
    
 
  
public function getBestSellerPackages($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $orders = Order::with('orderDetails.package.feedbacks')
        ->where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->get();

    $packageStats = [];

    foreach ($orders as $order) {
        foreach ($order->orderDetails as $detail) {
            $package = $detail->package;

            if ($package) {
                $packageId = $package->id;

                if (!isset($packageStats[$packageId])) {
                    $feedbackCount = $package->feedbacks->count();
                    $totalRating = $package->feedbacks->sum('rating');
                    $averageRating = $feedbackCount > 0 ? round($totalRating / $feedbackCount, 2) : null;

                    $packageStats[$packageId] = [
                        'id' => $packageId,
                        'name' => $package->name,
                        'photo' => $package->photo,
                        'price' => $package->base_price,
                        'order_count' => 0,
                        'feedback_count' => $feedbackCount,
                        'average_rating' => $averageRating,
                    ];
                }

                $packageStats[$packageId]['order_count'] += 1;
            }
        }
    }

    // ترتيب حسب عدد الطلبات
    $result = array_values($packageStats);
    usort($result, fn($a, $b) => $b['order_count'] <=> $a['order_count']);

    return response()->json([
        'branch' => $branch->location_note ?? $branch->description,
        'best_selling_packages' => $result
    ]);
}


public function getCustomerWithOrders($user_id)
{
    $manager = Auth::user();

    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 403);
    }

    $user = User::find($user_id);

    if (!$user) {
        return response()->json(['message' => 'المستخدم غير موجود.'], 404);
    }

    $orders = $user->orders()
        ->where('branch_id', $branch->id)
        ->with(['orderDetails.package', 'branch'])
        ->get();

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'phone' => $user->phone ?? null,
            'created_at' => $user->created_at,
        ],
        'orders' => $orders->map(function ($order) {
            return [
                'order_id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'created_at' => $order->created_at,
                'items' => $order->orderDetails->map(function ($detail) {
                    $package = $detail->package;

                    return [
                        'package_name' => $package->name ?? 'غير معروف',
                        'quantity' => $detail->quantity,
                        'unit_price' => $detail->unit_price,
                        'photo' => $package->photo 
                            ? asset('storage/' . $package->photo)
                            : null,
                    ];
                }),
            ];
        }),
    ]);
}


public function getBranchCustomers($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }
    $orders = Order::with('user')
        ->where('branch_id', $branch_id)
        ->get();

    $customers = [];

    foreach ($orders as $order) {
        $user = $order->user;
        if ($user) {
            if (!isset($customers[$user->id])) {
                $customers[$user->id] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'status ' => $user->status ,
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
    public function searchCustomersByName($branch_id, Request $request)
    {
        $name = $request->query('name'); 

        $branch = Branch::find($branch_id);

        if (!$branch || Auth::user()->id != $branch->manager_id) {
            abort(403, 'Unauthorized access');
        }

        $orders = Order::with('user')
            ->where('branch_id', $branch_id)
            ->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'LIKE', '%' . $name . '%');
            })
            ->get();

        $customers = [];

        foreach ($orders as $order) {
            $user = $order->user;
            if ($user && !isset($customers[$user->id])) {
                $customers[$user->id] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'customer_since' => $user->created_at->format('Y-m-d'),
                   'status' => $user->status ,
                    'total_orders' => 0,
                ];
            }

            if ($user) {
                $customers[$user->id]['total_orders']++;
            }
        }

        return response()->json(array_values($customers));
    }


    public function getCustomersVerifiedOnDate($branch_id, Request $request)
{
    $date = $request->query('date'); 

    $branch = Branch::find($branch_id);

    if (!$branch || Auth::user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $orders = Order::with('user')
        ->where('branch_id', $branch_id)
        ->whereHas('user', function ($query) use ($date) {
            $query->whereDate('email_verified_at', $date);
        })
        ->get();

    $customers = [];

    foreach ($orders as $order) {
        $user = $order->user;
        if ($user && !isset($customers[$user->id])) {
            $customers[$user->id] = [
                'id' => $user->id,
                'name' => $user->name,
                'status ' => $user->status ,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'verified_at' => $user->email_verified_at?->format('Y-m-d H:i') ?? null,
                'customer_since' => $user->created_at->format('Y-m-d'),
                'total_orders' => 0,
            ];
        }

        if ($user) {
            $customers[$user->id]['total_orders']++;
        }
    }

    return array_values($customers);
}
public function searchCustomersByStatus($branch_id, Request $request)
{
    $status = $request->query('status'); 

    $branch = Branch::find($branch_id);

    if (!$branch || Auth::user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $orders = Order::with('user')
        ->where('branch_id', $branch_id)
        ->whereHas('user', function ($query) use ($status) {
            $query->where('status', $status);
        })
        ->get();

    $customers = [];

    foreach ($orders as $order) {
        $user = $order->user;
        if ($user && !isset($customers[$user->id])) {
            $customers[$user->id] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'status' => $user->status ?? 'غير معروف',
                'customer_since' => $user->created_at->format('Y-m-d'),
                'total_orders' => 0,
            ];
        }

        if ($user) {
            $customers[$user->id]['total_orders']++;
        }
    }

    return response()->json(array_values($customers));
}




public function getPopularPackagesThisWeek($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $oneWeekAgo = Carbon::now()->subDays(7);

    $orders = Order::with('orderDetails.package.feedbacks')
        ->where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->where('created_at', '>=', $oneWeekAgo)
        ->get();

    $packageStats = [];

    foreach ($orders as $order) {
        foreach ($order->orderDetails as $detail) {
            $package = $detail->package;

            if ($package) {
                $packageId = $package->id;

                if (!isset($packageStats[$packageId])) {
                    $feedbackCount = $package->feedbacks->count();
                    $totalRating = $package->feedbacks->sum('rating');
                    $averageRating = $feedbackCount > 0 ? round($totalRating / $feedbackCount, 2) : null;

                    $packageStats[$packageId] = [
                        'id' => $packageId,
                        'name' => $package->name,
                        'photo' => $package->photo,
                        'price' => $package->base_price,
                        'order_count' => 0,
                        'feedback_count' => $feedbackCount,
                        'average_rating' => $averageRating,
                    ];
                }

                $packageStats[$packageId]['order_count'] += 1;
            }
        }
    }

    $result = array_values($packageStats);
    usort($result, fn($a, $b) => $b['order_count'] <=> $a['order_count']);

    return response()->json([
        'branch' => $branch->location_note ?? $branch->description,
        'packages' => $result
    ]);
}






}
