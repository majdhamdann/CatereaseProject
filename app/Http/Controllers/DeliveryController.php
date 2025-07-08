<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DeliveryController extends Controller
{
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


        $deliveries = Delivery::with(['order.user', 'order.branch'])
            ->where('delivery_person_id', $deliveryPerson->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $deliveries->count(),
            'data' => $deliveries->map(function ($delivery) {
                return [
                    'delivery_id' => $delivery->id,
                    'status' => $delivery->status,
                    'estimated_time' => $delivery->estimated_time,
                    'delivered_at' => $delivery->delivered_at,
                    'notes' => $delivery->notes,
                    'order' => [
                        'id' => $delivery->order->id,
                        'user' => $delivery->order->user->name ?? null,
                        'branch' => $delivery->order->branch->description ?? null,
                        'total_price' => $delivery->order->total_price,
                        'created_at' => $delivery->order->created_at,
                    ]
                ];
            }),
        ]);
    }
}
