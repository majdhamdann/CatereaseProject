<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
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

//    public function assignedOrderDetails($orderId)
//    {
//        try {
//            DB::beginTransaction();
//
//            $user = Auth::user();
//            $deliveryPerson = $user->deliveryPerson;
//
//            if (!$deliveryPerson) {
//                return response()->json([
//                    'status' => 'error',
//                    'message' => 'You are not a delivery person.'
//                ], 403);
//            }
//
//            $delivery = Delivery::with([
//                'order.orderDetails.package',
//                'order.user',
//                'order.address.city',
//                'order.branch.restaurant',
//            ])
//                ->where('delivery_person_id', $deliveryPerson->id)
//                ->whereHas('order', fn($q) => $q->where('id', $orderId))
//                ->first();
//
//            if (!$delivery) {
//                return response()->json([
//                    'status' => 'error',
//                    'message' => 'Order not found or not assigned to you.'
//                ], 404);
//            }
//
//            $order = $delivery->order;
//            $address = $order->address;
//            $city = optional($address->city);
//            $branch = $order->branch;
//            $restaurant = optional($branch)->restaurant;
//
//            $itemsSummary = $order->orderDetails->map(function ($detail) {
//                return optional($detail->package)->name . ' (' . $detail->quantity . ')';
//            })->implode(', ');
//
//            DB::commit();
//
//            return response()->json([
//                'status' => 'success',
//                'data' => [
//                    'order_id'        => $order->id,
//                    'status'          => $delivery->status,
//                    'total_price'     => number_format($order->total_price, 2),
//                    'created_at'      => $order->created_at->format('Y-m-d H:i'),
//                    'created_since'   => $order->created_at->diffForHumans(),
//                    'customer_name'   => $order->user->name ?? 'Unknown',
//                    'branch_name'     => $branch->description ?? 'N/A',
//                    'restaurant_name' => $restaurant->name ?? 'N/A',
//                    'items'           => $itemsSummary,
//                    'address' => [
//                        'address_id' => $address->id ?? null,
//                        'city'       => $city->name ?? null,
//                        'country'    => $city->country ?? null,
//                        'street'     => $address->street ?? null,
//                        'building'   => $address->building ?? null,
//                        'floor'      => $address->floor ?? null,
//                        'apartment'  => $address->apartment ?? null,
//                        'latitude'   => $address->latitude ?? null,
//                        'longitude'  => $address->longitude ?? null,
//                    ]
//                ]
//            ]);
//        } catch (\Throwable $e) {
//            DB::rollBack();
//
//            return response()->json([
//                'status' => 'error',
//                'message' => 'Failed to fetch order details.',
//                'error' => $e->getMessage(),
//            ], 500);
//        }
//    }
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



}
