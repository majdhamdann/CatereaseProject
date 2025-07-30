<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DeliveryController extends Controller
{
//    public function assignedOrders()
//    {
//        $user = Auth::user();
//
//
//        $deliveryPerson = $user->deliveryPerson;
//
//        if (!$deliveryPerson) {
//            return response()->json([
//                'status' => 'error',
//                'message' => 'You are not a delivery person.'
//            ], 403);
//        }
//
//
//        $deliveries = Delivery::with(['order.user', 'order.branch'])
//            ->where('delivery_person_id', $deliveryPerson->id)
//            ->orderBy('created_at', 'desc')
//            ->get();
//
//        return response()->json([
//            'status' => 'success',
//            'count' => $deliveries->count(),
//            'data' => $deliveries->map(function ($delivery) {
//                return [
//                    'delivery_id' => $delivery->id,
//                    'status' => $delivery->status,
//                    'estimated_time' => $delivery->estimated_time,
//                    'delivered_at' => $delivery->delivered_at,
//                    'notes' => $delivery->notes,
//                    'order' => [
//                        'id' => $delivery->order->id,
//                        'user' => $delivery->order->user->name ?? null,
//                        'branch' => $delivery->order->branch->description ?? null,
//                        'total_price' => $delivery->order->total_price,
//                        'created_at' => $delivery->order->created_at,
//                    ]
//                ];
//            }),
//        ]);
//    }
//

    public function assignedOrders()
    {
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
            'order.branch'
        ])
            ->where('delivery_person_id', $deliveryPerson->id)
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


            $translatedStatus = match(strtolower($delivery->status ?? '')) {
                'pending'   => 'Pending',
                'approved'  => 'Approved',
                'rejected'  => 'Rejected',
                'canceled'  => 'Canceled',
                'completed' => 'Completed',
                default     => $order->status ?? 'Unknown',
            };

            return [
                'order_id'      => $order->id,
                'customer_name' => $order->user->name ?? 'Unknown',
                'branch_name'   => $order->branch->description ?? 'Not Available',
                'status'        => $translatedStatus,
                'total_price'   => number_format($order->total_price, 2),
                'created_at'    => $order->created_at->format('Y-m-d H:i'),
                'created_since' => $order->created_at->diffForHumans(),
               // 'items'         => $itemsSummary,
            ];
        })->filter();

        return response()->json([
            'status' => 'success',
            'count'  => $data->count(),
            'data'   => $data->values(),
        ]);
    }

    public function assignedOrderDetails($orderId)
    {
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

        $order = $delivery->order;
        $address = $order->address;
        $city = optional($address->city);
        $branch = $order->branch;
        $restaurant = optional($branch)->restaurant;

        $itemsSummary = $order->orderDetails->map(function ($detail) {
            return optional($detail->package)->name . ' (' . $detail->quantity . ')';
        })->implode(', ');

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_id'        => $order->id,
                'status'          => ucfirst($delivery->status),
                'total_price'     => number_format($order->total_price, 2),
                'created_at'      => $order->created_at->format('Y-m-d H:i'),
                'created_since'   => $order->created_at->diffForHumans(),
                'customer_name'   => $order->user->name ?? 'Unknown',
                'branch_name'     => $branch->description ?? 'N/A',
                'restaurant_name' => $restaurant->name ?? 'N/A',
                'items'           => $itemsSummary,
                'address' => [
                    'address_id' => $address->id ?? null,
                    'city'       => $city->name ?? null,
                    'country'    => $city->country ?? null,
                    'street'     => $address->street ?? null,
                    'building'   => $address->building ?? null,
                    'floor'      => $address->floor ?? null,
                    'apartment'  => $address->apartment ?? null,
                    'latitude'   => $address->latitude ?? null,
                    'longitude'  => $address->longitude ?? null,
                ]
            ]
        ]);
    }





}
