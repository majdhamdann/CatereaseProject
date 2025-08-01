<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Delivery;
use App\Models\DeliveryPerson;
use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class OrderManagementController extends Controller
{
    public function index()
{
    $manager = auth()->user();
    
    $branch = Branch::where('manager_id', $manager->id)->first();
    if (!$branch) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مسجل لهذا المدير'
        ], 404);
    }

    $orders = Order::with(['orderDetails.package', 'branch'])
        ->where('branch_id', $branch->id)
        ->orderBy('created_at', 'desc')
        ->get();

    if ($orders->isEmpty()) {
        return response()->json([
            'status' => true,
            'message' => 'لا توجد طلبات مسجلة لهذا الفرع',
            'branch_id' => $branch->id,
            'orders' => []
        ]);
    }

    return response()->json([
        'status' => true,
        'branch_id' => $branch->id,
        'orders_count' => $orders->count(),
        'orders' => $orders
    ]);
}
    public function approve($id)
{
    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id');

    $order = Order::where('id', $id)->where('branch_id', $branchId)->firstOrFail();

    $order->is_approved = true;
    $order->rejection_reason = null; // Clear rejection reason when approving
    $order->approved_by = $manager->id;
    $order->approved_at = now();
    $order->save();

    return response()->json(['message' => 'Order approved successfully']);
    }

    public function reject(Request $request, $id)
{
    $request->validate([
        'rejection_reason' => 'required|string',
    ]);

    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id');

    $order = Order::where('id', $id)->where('branch_id', $branchId)->firstOrFail();

    $order->is_approved = false; 
    $order->rejection_reason = $request->rejection_reason;
    $order->approved_by = $manager->id;
    $order->approved_at = now();
    $order->save();

    return response()->json(['message' => 'Order rejected']);
    }
    public function updateStatus(Request $request, $id)
   {
    $request->validate([
        'status' => 'required|in:pending,confirmed,preparing,delivered,cancelled',
    ]);

    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id'); // ✅ هنا التعديل

    $order = Order::where('id', $id)
        ->where('branch_id', $branchId)
        ->firstOrFail();

    $oldStatus = $order->status;
    $order->status = $request->status;
    $order->save();

    return response()->json([
        'message' => 'Order status updated',
        'from' => $oldStatus,
        'to' => $order->status,
    ]);
   }
public function getAvailableDeliveryPersons()
{
    $availableDeliveryPersons = DeliveryPerson::with('user')
        ->where('is_available', 1)
        ->get()
        ->map(function ($deliveryPerson) {
            return [
                'id' => $deliveryPerson->id,
                'user_id' => $deliveryPerson->user_id,
                'name' => $deliveryPerson->user->name ?? null,
                'phone' => $deliveryPerson->user->phone ?? null,
            ];
        });

    return response()->json([
        'status' => true,
        'available_delivery_persons' => $availableDeliveryPersons,
    ]);
}


 public function stateOrder($status)
    {
        $manager = auth()->user();
        $branchId = Branch::where('manager_id', $manager->id)->value('id');
         if (!$branchId) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير.'
           ], 403);
        }
        $order = Order::with(['orderDetails.package'])
            ->where('branch_id', $branchId)
            ->where('status',$status)
            ->firstOrFail();

        return response()->json($order);
    }
public function assignDeliveryPerson(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'delivery_person_id' => 'required|exists:delivery_people,id',
        'estimated_minutes' => 'required|integer|min:10' 
    ]);

    DB::beginTransaction();
    try {
        $manager = auth()->user();
        $branch = Branch::where('manager_id', $manager->id)->firstOrFail();

        $order = Order::where('id', $validated['order_id'])
                    ->where('branch_id', $branch->id)
                    ->where('is_approved', true)
                    ->lockForUpdate()
                    ->firstOrFail();

        $deliveryPerson = DeliveryPerson::where('id', $validated['delivery_person_id'])
                                    ->where('is_available', true)
                                    ->lockForUpdate()
                                    ->firstOrFail();

        $estimatedTime = now()->addMinutes($validated['estimated_minutes']);

        $delivery = Delivery::create([
            'order_id' => $order->id,
            'delivery_person_id' => $deliveryPerson->id,
            'status' => 'assigned',
            'estimated_time' => $estimatedTime, 
            'assigned_at' => now(),
        ]);

        $deliveryPerson->update(['is_available' => false]);
        
        $order->update([
            'status' => 'assigned',
            'delivery_id' => $delivery->id,
            'updated_at' => now()
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين موظف التوصيل بنجاح',
            'estimated_delivery_time' => $estimatedTime->format('Y-m-d H:i:s'),
            'data' => [
                'order' => $order->fresh(),
                'delivery' => $delivery,
                'delivery_person' => $deliveryPerson->fresh()
            ]
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'فشل التعيين: ' . $e->getMessage()
        ], 500);
    }
}
public function show($id)
{
    $manager = auth()->user();

    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير'
        ], 403);
    }

    try {
        $order = Order::with([
            'orderDetails.package.extras',
            'orderDetails.package.occasionTypes',
            
        ])
        ->where('id', $id)
        ->where('branch_id', $branch->id)
        ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود أو لا ينتمي لفرعك',
                'order_id' => $id,
                'branch_id' => $branch->id
            ], 404);
        }

        return response()->json([
            'status' => true,
            'order' => $order
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء تحميل الطلب.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function getBranchOrderStatistics()
{
   $manager = auth()->user();

    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير'
        ], 403);
    }


    $totalOrders = Order::where('branch_id', $branch->id)->count();

    $totalBalance = Order::where('branch_id', $branch->id)
        ->whereIn('status', ['confirmed', 'preparing', 'delivered'])
        ->sum('total_price');

    /*$averageSatisfaction = Feedback::whereHas('order', function ($query) use ($branch) {
        $query->where('branch_id', $branch->id);
    })->avg('rating'); */

    return response()->json([
        'total_orders' => $totalOrders,
        'total_balance' => $totalBalance,
        //'average_satisfaction' => round($averageSatisfaction, 2)
    ]);
}


}
