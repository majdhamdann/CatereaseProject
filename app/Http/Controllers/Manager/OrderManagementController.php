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
    
    $branch = Branch::where('manager_id', $manager->id)

             ->first();
    if (!$branch) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مسجل لهذا المدير'
        ], 404);
    }

    $orders = Order::with(['orderDetails.package', 'branch'])
        ->where('branch_id', $branch->id)
        ->where('is_submitted', true)
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

    $order = Order::where('id', $id)->where('is_submitted', true)
    ->where('branch_id', $branchId)->firstOrFail();

    $order->is_approved = true;
    $order->status = 'preparing';
    $order->rejection_reason = null;
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

    $order = Order::where('id', $id)->where('is_submitted', true)
           ->where('branch_id', $branchId)->firstOrFail();

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
        ->where('is_submitted', true)
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
        $order = Order::with(['orderDetails.package','branch'])
            ->where('branch_id', $branchId)
            ->where('is_submitted', true)
            ->where('status',$status)
            ->firstOrFail();

        return response()->json($order);
      }
     public function assignDeliveryPerson(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'delivery_person_id' => 'required|exists:delivery_people,id',
    ]);

    DB::beginTransaction();
    try {
        $manager = auth()->user();
        $branch = Branch::where('manager_id', $manager->id)->firstOrFail();

        $order = Order::where('id', $validated['order_id'])
                    ->where('branch_id', $branch->id)
                    ->where('is_approved', true)
                    ->where('is_submitted', true)
                    ->lockForUpdate()
                    ->firstOrFail();

        $deliveryPerson = DeliveryPerson::where('id', $validated['delivery_person_id'])
                                    ->where('is_available', true)
                                    ->lockForUpdate()
                                    ->firstOrFail();


        $delivery = Delivery::create([
            'order_id' => $order->id,
            'delivery_person_id' => $deliveryPerson->id,
            'status' => 'assigned',
           // 'estimated_time' => $estimatedTime, 
            'assigned_at' => now(),
        ]);

        $deliveryPerson->update(['is_available' => false]);
        
        $order->update([
            'status' => 'preparing',
            'delivery_id' => $delivery->id,
            'updated_at' => now()
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'تم تعيين موظف التوصيل بنجاح',
          //  'estimated_delivery_time' => $estimatedTime->format('Y-m-d H:i:s'),
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
            'user',
            'address.city',
            'orderDetails.package',
            'orderDetails.package.occasionTypes',
            'orderDetails.extras.extra',
            'orderDetails.services.service.serviceType',
            'services.branchServiceType.serviceType',
            'delivery.deliveryPerson' 
        ])
        ->where('id', $id)
        ->where('branch_id', $branch->id)
        ->where('is_submitted', true)
        ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود أو لا ينتمي لفرعك',
                'order_id' => $id,
                'branch_id' => $branch->id
            ], 404);
        }
        
        $deliveryPrice = $order->deliveryArea?->delivery_price;
        $orderPrice = $order->total_price;
        $totalPriceWithDelivery = $deliveryPrice + $orderPrice;
    
        $formatted = [
            'id' => $order->id,
            'customer' => [
                'name' => $order->user->name,
                'phone' => $order->user->phone ?? null,
                'email' => $order->user->email ?? null,
                'gender' => $order->user->gender ?? null,
                'address' => [
                    'city' => $order->address->city->name ?? null,
                    'street' => $order->address->street ?? null,
                    'building' => $order->address->building ?? null,
                    'floor' => $order->address->floor ?? null,
                    'apartment' => $order->address->apartment ?? null,
                    'latitude' => $order->address->latitude,
                    'longitude' => $order->address->longitude,
                ],
                'delivery_time' => $order->delivery_time,
                'is_approved' => $order->is_approved,
                'approved_at' => $order->approved_at,
                'rejection_reason' => $order->rejection_reason,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'notes_order' => $order->notes,
                'deliveryPrice' => $deliveryPrice,
            ],
            'payment' => [
                'totalPriceWithDelivery' => $totalPriceWithDelivery,
                'deliveryPrice' => $deliveryPrice,
                'orderPrice' => $orderPrice,
            ],
            'delivery_info' => $order->delivery ? [
                'status' => $order->delivery->status,
                'assigned_at' => $order->delivery->assigned_at,
                'estimated_time' => $order->delivery->estimated_time, 
                'delivery_person' => $order->delivery->deliveryPerson ? [
                    'name' => $order->delivery->deliveryPerson->name,
                    'phone' => $order->delivery->deliveryPerson->phone
                ] : null
            ] : null,
            'details' => $order->orderDetails->map(function ($detail) {
                return [
                    'package_id' => $detail->package->id,
                    'package_name' => $detail->package->name ?? null,
                    'package_photo' => $detail->package->photo ?? null,
                    'quantity' => $detail->quantity,
                    'categories' => $detail->package->categories->map(function ($categories) {
                        return [
                            'id' => $categories->id,
                            'name' => $categories->name,
                        ];
                    }),
                    'unit_price' => $detail->unit_price,
                    'extra_persons' => $detail->extra_persons,
                    'occasion_type' => $detail->package->occasionTypes->first()->name ?? null,
                    'extras' => $detail->extras->map(function ($extra) {
                        return [
                            'name' => $extra->extra->name ?? null,
                            'quantity' => $extra->quantity,
                            'unit_price' => $extra->unit_price,
                            'total_price' => $extra->total_price,
                        ];
                    }),
                    'services' => $detail->services->map(function ($service) {
                        return [
                            'name' => $service->service->serviceType->name ?? null,
                            'custom_price' => $service->custom_price,
                        ];
                    }),
                ];
            }),
            'general_services' => $order->services->map(function ($service) {
                return [
                    'name' => $service->branchServiceType->serviceType->name ?? null,
                    'quantity' => $service->quantity,
                    'total_price' => $service->total_price,
                ];
            }),
        ];

        return response()->json([
            'status' => true,
            'order' => $formatted
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


    $totalOrders = Order::where('branch_id', $branch->id)
     ->whereIn('status', ['delivered'])->count();

    $totalBalance = Order::where('branch_id', $branch->id)
        ->whereIn('status', ['confirmed', 'preparing', 'delivered'])
        ->sum('total_price');

    return response()->json([
        'total_orders_delivered' => $totalOrders,
        'total_balance' => $totalBalance,

    ]);
     }
     public function OrderWithData(Request $request)
{
    $request->validate([
        'date' => 'required|date',
    ]);

    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id');

    if (!$branchId) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير.'
        ], 403);
    }

    $orders = Order::with(['orderDetails.package'])
        ->where('branch_id', $branchId)
        ->where('is_submitted', true)
        ->whereDate('created_at', $request->date)
        ->get();

    if ($orders->isEmpty()) {
        return response()->json([
            'status' => true,
            'message' => 'لا توجد طلبات في هذا التاريخ.',
            'date' => $request->date,
            'data' => []
        ]);
    }

    $grouped = $orders->groupBy('status');

    return response()->json([
        'status' => true,
        'date' => $request->date,
        'data' => $grouped
    ]);
      }

     public function allStatesOrders()
{
    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id');

    if (!$branchId) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير.'
        ], 403);
    }

    $statuses = ['pending', 'confirmed', 'preparing', 'delivered', 'cancelled'];

    $ordersByStatus = [];

    foreach ($statuses as $status) {
        $ordersByStatus[$status] = Order::with(['orderDetails.package'])
            ->where('branch_id', $branchId)
            ->where('is_submitted', true)
            ->where('status', $status)
            ->get();
    }

    return response()->json($ordersByStatus);
      }
     public function latestDeliveredOrders()
{
    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id');

    if (!$branchId) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير.'
        ], 403);
    }

    $orders = Order::with(['orderDetails.package'])
        ->where('branch_id', $branchId)
        ->where('is_submitted', true)
        ->where('status', 'delivered')
        ->orderBy('created_at', 'desc') 
        ->get();

    return response()->json($orders);
     }


}
