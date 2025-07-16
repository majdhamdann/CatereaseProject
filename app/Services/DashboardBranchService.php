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

    if (!$branch || auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $lastOrder = Order::where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->orderByDesc('created_at')
        ->first();

    $income = $lastOrder ? (float) $lastOrder->total_price : 0;

    $totalIncome = Order::where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->sum('total_price');

    return response()->json([
        'branch_id' => (int) $branch_id,
        'income' => $income,
        'total_income' => (float) $totalIncome,
    ]);
}
  public function getMyBranch1()
{
    $user = auth()->user();

    $branch = Branch::with([
        'categories:id,name',                             // الأصناف المرتبطة
        'branchServiceTypes.serviceType:id,name,description', // مناطق التوصيل
        'manager:id,phone'                                // مدير الفرع
    ])
    ->where('manager_id', $user->id)
    ->first();

    if (!$branch) {
        return response()->json(['message' => 'No branch found for this manager'], 404);
    }

    return response()->json([
        'branch' => [
            'id' => $branch->id,
            'location_note' => $branch->location_note,
            'description' => $branch->description,
            'phone' => $branch->manager->phone ?? null,

            'categories' => $branch->categories->pluck('name'),

            'delivery_regions' => $branch->branchServiceTypes->map(function ($service) {
                return [
                    'name' => $service->serviceType->name ?? 'غير معروف',
                    'description' => $service->serviceType->description ?? '',
                    'price' => $service->custom_price ?? $service->service_cost ?? 0,
                ];
            }),
        ]
    ]);
}
public function getMyBranch()
{
    $user = auth()->user();

    $branch = Branch::with([
        'categories:id,name',                             
        'branchServiceTypes.serviceType:id,name,description', 
        'manager:id,phone,name'                       
    ])
    ->where('manager_id', $user->id)
    ->first();

    if (!$branch) {
        return response()->json(['message' => 'No branch found for this manager'], 404);
    }

    return response()->json([
        'branch' => [
            'id' => $branch->id,
            'location_note' => $branch->location_note,
            'description' => $branch->description,
            'phone' => $branch->manager->phone ?? null,
            'manager_name' => $branch->manager->name ?? null,  
            'restaurant_photo' => $branch->restaurant->photo ?? null,  
            
            'categories' => $branch->categories->pluck('name'),

            'delivery_regions' => $branch->branchServiceTypes->map(function ($service) {
                return [
                    'name' => $service->serviceType->name ?? 'غير معروف',
                    'description' => $service->serviceType->description ?? '',
                    'price' => $service->custom_price ?? $service->service_cost ?? 0,
                ];
            }),
        ]
    ]);
}


    public function getLastDeliveredOrdersWithRatings($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $orders = Order::with(['orderDetails.foodItem']) // eager load to avoid N+1 queries
        ->where('branch_id', $branch_id)
        ->where('status', 'delivered')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    $results = [];

    foreach ($orders as $order) {
        foreach ($order->orderDetails as $detail) {
            $food = $detail->foodItem;

            if (!$food) {
                continue;
            }

            $feedbacksQuery = $food->feedbacks();
            if (!$feedbacksQuery) {
                continue;
            }

            $feedbacks = $feedbacksQuery->where('type', 'rating')->get();

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

        if (!$branch || Auth()->user()->id != $branch->manager_id) {
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
        $orders = Order::with(['orderDetails.package.categories'])
            ->where('status', 'delivered')
            ->where('branch_id', $branch_id)
            ->get();

        $totalDeliveredItems = 0;
        $categoryCounts = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $categoryName = optional($detail->package->categories->first())->name ?? 'غير معروف';


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

  public function getPopularPackageCategories($branch_id)
{
    $branch = Branch::find($branch_id);

    if (!$branch || Auth()->user()->id != $branch->manager_id) {
        abort(403, 'Unauthorized access');
    }

    $orders = Order::with(['orderDetails.package.categories']) // ✅ تحميل التصنيفات المرتبطة بالباكجات
        ->where('branch_id', $branch_id)
        ->get();

    $categoryUserMap = [];

    foreach ($orders as $order) {
        $userId = $order->user_id;

        foreach ($order->orderDetails as $detail) {
            if ($detail->package && $detail->package->categories) {
                foreach ($detail->package->categories as $category) {
                    $categoryName = $category->name ?? 'غير معروف';
                    $categoryUserMap[$categoryName][$userId] = true;
                }
            } else {
                $categoryUserMap['غير معروف'][$userId] = true;
            }
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
