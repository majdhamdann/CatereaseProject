<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{

    public function assignedOrders()
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $deliveryPerson = $user->deliveryPerson;

            if (!$deliveryPerson) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not a delivery person.'
                ], 403);
            }

            $deliveries = Delivery::with([
                'order.orderDetails.package',
                'order.user',
                'order.branch',
                'order.branch.restaurant'
            ])
                ->where('delivery_person_id', $deliveryPerson->id)
                ->where('status', '!=', 'pending')
                ->latest()
                ->get();

            $data = $deliveries->map(function ($delivery) {
                $order = $delivery->order;

                if (!$order) {
                    return null;
                }

                $itemsSummary = $order->orderDetails->map(function ($detail) {
                    $packageName = $detail->package->name ?? 'Unknown Package';
                    return $packageName . ' (' . $detail->quantity . ')';
                })->implode(', ');

                return [
                    'order_id'      => $order->id,
                    'customer_name' => $order->user->name ?? 'Unknown',
                    'restaurant_name' => optional($order->branch->restaurant)->name ?? 'Unknown',
                    'branch_name'   => $order->branch->description ?? 'Not Available',
                    'status'        => $delivery->status,
                    'total_price'   => number_format($order->total_price, 2),
                    'created_at'    => $order->created_at->format('Y-m-d H:i'),
                    'created_since' => $order->created_at->diffForHumans(),
                    // 'items'      => $itemsSummary,
                ];
            })->filter();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'count'  => $data->count(),
                'data'   => $data->values(),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while fetching orders.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function assignedOrderDetails($orderId)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $deliveryPerson = $user->deliveryPerson;

            if (!$deliveryPerson) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not a delivery person.'
                ], 403);
            }

            $delivery = Delivery::with([
                'order.orderDetails.package',
                'order.user',
                'order.address.city',
                'order.branch.restaurant',
            ])
                ->where('delivery_person_id', $deliveryPerson->id)
                ->whereHas('order', fn($q) => $q->where('id', $orderId))
                ->first();

            if (!$delivery) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found or not assigned to you.'
                ], 404);
            }

            DB::commit();


            return response()->json([
                'status' => 'success',
                'data'   => $delivery
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch order details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show()
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $deliveryPerson = $user->deliveryPerson;

            if (!$deliveryPerson) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not registered as a delivery person.'
                ], 403);
            }


            $branches = $deliveryPerson->branches()->with('restaurant')->get();


            $restaurants = $branches->pluck('restaurant')->filter();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $user,
                    'delivery_person' => $deliveryPerson,
                    'branches' => $branches,
                    'restaurants' => $restaurants
                ]
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch profile details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateDeliveryStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,on_the_way_to_pickup,picked_up,delivered,on_the_way,cancelled,failed'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $deliveryPerson = $user->deliveryPerson;

            if (!$deliveryPerson) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not a delivery person.'
                ], 403);
            }


            $delivery = Delivery::where('delivery_person_id', $deliveryPerson->id)
                ->whereHas('order', fn($q) => $q->where('id', $orderId))
                ->first();

            if (!$delivery) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found or not assigned to you.'
                ], 404);
            }


            $delivery->status = $request->status;
            $delivery->save();


            if ($request->status === 'delivered') {
                $delivery->order->status = 'delivered';
                $delivery->order->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery status updated successfully',
                'data' => $delivery
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update delivery status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function confirmByQr(Request $request)
    {
        $request->validate([
            'qr_string' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            $deliveryPerson = $user->deliveryPerson;

            if (!$deliveryPerson) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not a delivery person.'
                ], 403);
            }

            $order = Order::where('qr_token', $request->qr_string)->first();

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid QR code or order not found.'
                ], 404);
            }

            $delivery = Delivery::where('order_id', $order->id)
                ->where('delivery_person_id', $deliveryPerson->id)
                ->first();

            if (!$delivery) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not assigned to you.'
                ], 403);
            }

            DB::transaction(function () use ($delivery, $order, $request) {
               // $delivery->qr_scanned_string = $request->qr_string;
                $delivery->status = 'delivered';
                $delivery->delivered_at = now();
                $delivery->save();

                $order->status = 'delivered';
                $order->save();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery confirmed by QR successfully.',
                //'data' => $delivery
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to confirm delivery.',
                'error' => $e->getMessage()
            ], 500);
        }
    }






}
