<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Delivery;
use App\Models\DeliveryPerson;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Payment;
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
    $order->status = 'confirmed';
    $order->rejection_reason = null;
    $order->approved_by = $manager->id;
    $order->approved_at = now();
    $order->save();
    $bill=Bill::create([
        'order_id' => $order->id,
        'user_id' => $order->user_id,
        'amount' => $order->total_price,
        'issued_at' => now(),
        'status' => 'unpaid',
    ]);
            // إرسال إشعار للزبون
            
        if ($order->user && $order->user->device_token) {
           $this->unicast((object)[
             'title' => 'Your order has been approved ',
             'body'  => "Your order #{$order->id} has been confirmed and is being prepared."
           ], $order->user->device_token);
}


    return response()->json(
        [
            'message' => 'Order approved successfully',
           'bill' => $bill
    ]);
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
    $order->status = 'cancelled';
    $order->rejection_reason = $request->rejection_reason;
    $order->approved_by = $manager->id;
    $order->approved_at = now();
    $order->save();
        if ($order->user && $order->user->device_token) {
              $this->unicast((object)[
                'title' => 'Your order was rejected ',
                'body'  => "Your order #{$order->id} has been rejected. Reason: {$request->rejection_reason}"
    ], $order->user->device_token);
}


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
                                    ->lockForUpdate()
                                    ->firstOrFail();

        if (!$deliveryPerson->is_available) {
            throw new \Exception('موظف التوصيل غير متاح حالياً');
        }

        $previousDelivery = Delivery::where('order_id', $order->id)->first();

        if ($previousDelivery) {
            $previousDelivery->deliveryPerson->update(['is_available' => true]);

            $previousDelivery->delete();
        }

        $delivery = Delivery::create([
            'order_id' => $order->id,
            'delivery_person_id' => $deliveryPerson->id,
            'status' => 'assigned',
            'updated_at' => now(),
        ]);

        $deliveryPerson->update(['is_available' => false]);

        $order->update([
           //  'status' => 'preparing',
          //  'delivery_id' => $delivery->id,
            'updated_at' => now()
        ]);

        $message = 'تم تعيين موظف التوصيل بنجاح';
        if ($previousDelivery) {
            $message = 'تم تغيير موظف التوصيل بنجاح';
        }

        DB::commit();
         if ($deliveryPerson->user && $deliveryPerson->user->device_token) {
             $this->unicast((object)[
               'title' => 'New Delivery Assigned',
               'body'  => "You have been assigned to deliver order #{$order->id}. Please check your delivery app."
            ], $deliveryPerson->user->device_token);
         }
        return response()->json([
            'status' => true,
            'message' => $message,
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
            'message' => 'فشل العملية: ' . $e->getMessage()
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
            'delivery.deliveryPerson',
            'bill.payments' 
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

        $billInfo = null;
        if ($order->bill) {
            $billInfo = [
                'bill_id' => $order->bill->id,
                'amount' => $order->bill->amount,
                'status' => $order->bill->status,
                'created_at' => $order->bill->created_at,
                'issued_at' => $order->bill->issued_at,
                'updated_at' => $order->bill->updated_at,
                'payments' => $order->bill->payments->map(function ($payment) {
                    return [
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'payment_status' => $payment->payment_status,
                        'paid_at' => $payment->paid_at,
                        'created_at' => $payment->created_at
                    ];
                }),
                'total_paid' => $order->bill->payments->sum('amount'),
                'payment_progress' => [
                    'percentage' => $order->bill->original_amount > 0 ? 
                        (($order->bill->original_amount - $order->bill->amount) / $order->bill->original_amount) * 100 : 0,
                    'paid_amount' => $order->bill->original_amount - $order->bill->amount,
                    'remaining_amount' => $order->bill->amount
                ]
            ];
        }

        $formatted = [
            'id' => $order->id,
            'prepayment_paid' => $order->prepayment_paid,
            'prepayment_paid_at' => $order->prepayment_paid_at,
            'final_prepayment_paid' => $order->final_prepayment_paid,
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
                    'district' => $order->address->district->name ?? null,
                    'area' => $order->address->area->name ?? null,
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
                'bill_info' => $billInfo, 
            ],
            'delivery_info' => $order->delivery ? [
                'status' => $order->delivery->status,
                'delivered_at' => $order->delivery->delivered_at,
                'acceptance_status' => $order->delivery->acceptance_status,
                'rejection_reason' => $order->delivery->rejection_reason,
                'notes' => $order->delivery->notes,
                'delivery_person_id' => $order->delivery->deliveryPerson->id,
                'delivery_person' => $order->delivery ? [
                    'name' => $order->delivery->deliveryPerson->user->name,
                    'phone' => $order->delivery->deliveryPerson->user->phone
                ] : null
            ] : null,
            'details' => $order->orderDetails->map(function ($detail) {
                return [
                    'package_id' => $detail->package->id,
                    'package_name' => $detail->package->name ?? null,
                    'package_photo' => $detail->package->photo ?? null,
                    'prepayment_required' => $detail->package->prepayment_required ,
                    'prepayment_amount' => $detail->package->prepayment_amount ,
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

    $statuses = ['pending', 'confirmed', 'preparing', 'delivered', 'cancelled','waiting'];

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
     public function payCash(Request $request, $orderId)
{
    $request->validate([
        'payment_type' => 'required|in:partial,full',
        'amount' => 'required_if:payment_type,partial|numeric|min:0',
    ]);

    try {
        DB::beginTransaction();

        $manager = auth()->user();
        $branch = Branch::where('manager_id', $manager->id)->first();

        if (!$branch) {
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد فرع مرتبط بهذا المدير.'
            ], 403);
        }

        $order = Order::where('id', $orderId)
            ->where('branch_id', $branch->id)
            ->where('is_submitted', true)
            ->firstOrFail();

        $bill = Bill::where('order_id', $order->id)->first();

        if (!$bill) {
            return response()->json([
                'status' => false,
                'message' => 'لا توجد فاتورة مرتبطة بهذا الطلب.'
            ], 404);
        }

        if ($bill->status === 'paid') {
            return response()->json([
                'status' => false,
                'message' => 'الفاتورة مدفوعة بالكامل مسبقاً.'
            ], 400);
        }

        $paymentType = $request->payment_type;
        $amount = $paymentType === 'full' ? $bill->amount : $request->amount;

        if ($paymentType === 'partial' && $amount > $bill->amount) {
            return response()->json([
                'status' => false,
                'message' => 'المبلغ المدفوع يتجاوز المبلغ المتبقي في الفاتورة.'
            ], 400);
        }

        if ($paymentType === 'full') {
            $bill->status = 'paid';
            $bill->amount = 0;

            $order->final_payment_paid = true;
            $order->final_payment_paid_at = now();
        } elseif ($paymentType === 'partial') {
            $bill->amount -= $amount;
            $bill->status = $bill->amount > 0 ? 'partially_paid' : 'paid';

            $order->prepayment_paid = true;
            $order->prepayment_paid_at = now();
        }
        $order->status = 'preparing';
        $bill->save();
        $order->save();

        Payment::create([
            'bill_id' => $bill->id,
            'user_id' => $bill->user_id,
            'payment_method' => 'cash',
            'amount' => $amount,
            'payment_status' => 'completed',
            'paid_at' => now(),
            'paid_by' => $manager->id, 
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدفع النقدي بنجاح.',
            'data' => [
                'order_id' => $order->id,
                'bill_id' => $bill->id,
                'payment_type' => $paymentType,
                'amount_paid' => $amount,
                'remaining_amount' => $bill->amount,
                'bill_status' => $bill->status,
                'paid_by' => $manager->name
            ]
        ]);

    } catch (ModelNotFoundException $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'الطلب غير موجود أو لا ينتمي لفرعك.'
        ], 404);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'فشل في تسجيل الدفع.',
            'error' => $e->getMessage()
        ], 500);
    }
}


}
