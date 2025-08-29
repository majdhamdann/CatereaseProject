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

    use \App\Traits\FirebaseNotificationTrait;

    public function sendNotification(Request $request)
    {
        try {
            $request->validate([
                'deviceToken' => 'required|string',
                'title'       => 'required|string',
                'body'        => 'required|string',
            ]);

            $notification_data = (object) [
                'title' => $request->title,
                'body'  => $request->body,
            ];

            $response = $this->unicast($notification_data, $request->deviceToken);

            return response()->json([
                'status'   => true,
                'message'  => 'Notification sent successfully',
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

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
                'order.address.city',
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
                    'order' => [
                        'id' => $order->id,
                        'status' => $delivery->status,
                        'total_price' => number_format($order->total_price, 2),
                        'created_at' => $order->created_at->format('Y-m-d H:i'),
                        'created_since' => $order->created_at->diffForHumans(),
                        'items' => $itemsSummary,
                    ],
                    'user' => [
                        'id' => $order->user->id,
                        'name' => $delivery->order->user->name,
                        'phone' => $delivery->order->user->phone,
                        'address' => [
                            'id' => $delivery->order->address->id,
                            'city' => $delivery->order->address->city->name ?? null,
                            'street' => $delivery->order->address->street ?? null,
                            'building' => $delivery->order->address->building ?? null,
                            'floor' => $delivery->order->address->floor ?? null,
                            'apartment' => $delivery->order->address->apartment ?? null,
                            'latitude' => $delivery->order->address->latitude ?? null,
                            'longitude' => $delivery->order->address->longitude ?? null,

                        ],
                    ],
                    'restaurant' => [
                        'id' => $delivery->order->branch->restaurant->id,
                        'name' => $delivery->order->branch->restaurant->name,
                        'phone' => $delivery->order->branch->restaurant->phone_number ?? 'N/A',
                        'branch' => $delivery->order->branch->description ?? 'N/A',
                    ],
                ];
            })->filter();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'count' => $data->count(),
                'data' => $data->values(),
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

            $order = $delivery->order;
            $address = $order->address;
            $city = optional($address->city);
            $branch = $order->branch;
            $restaurant = optional($branch)->restaurant;

            $itemsSummary = $order->orderDetails->map(function ($detail) {
                return optional($detail->package)->name . ' (' . $detail->quantity . ')';
            })->implode(', ');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'status' => $delivery->status,
                        'total_price' => number_format($order->total_price, 2),
                        'created_at' => $order->created_at->format('Y-m-d'),
                        'created_since' => $order->created_at->diffForHumans(),
                        'items' => $itemsSummary,
                    ],
                    'user' => [
                        'id' => $order->user->id,
                        'name' => $delivery->order->user->name,
                        'phone' => $delivery->order->user->phone,
                        'address' => [
                            'id' => $delivery->order->address->id,
                            'city' => $delivery->order->address->city->name ?? null,
                            'street' => $delivery->order->address->street ?? null,
                            'building' => $delivery->order->address->building ?? null,
                            'floor' => $delivery->order->address->floor ?? null,
                            'apartment' => $delivery->order->address->apartment ?? null,
                            'latitude' => $delivery->order->address->latitude ?? null,
                            'longitude' => $delivery->order->address->longitude ?? null,

                        ],
                    ],
                    'restaurant' => [
                        'id' => $delivery->order->branch->restaurant->id,
                        'name' => $delivery->order->branch->restaurant->name,
                        'branch' => $delivery->order->branch->description ?? 'N/A',
                        'location' => $delivery->order->branch->location_note ?? '',
                    ],
                ]
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
            'status' => 'required|in:on_the_way,cancelled,failed'
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

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery status updated successfully',
               // 'data' => $delivery
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
            'qr_string' => 'required|string',
            'notes'     => 'nullable|string|max:1000'
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
                $delivery->status       = 'delivered';
                $delivery->delivered_at = now();
                $delivery->notes        = $request->notes;
                $delivery->save();

                $order->status = 'delivered';
                $order->save();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery confirmed by QR successfully.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to confirm delivery.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRejectionReasons()
    {

        $reasons = [
            [
                'vehicle_breakdown'   => 'Vehicle breakdown',
                'vehicle_accident'    => 'Traffic accident',
                'traffic_jam'         => 'Traffic jam',
                'health_emergency'    => 'Health emergency',
                'personal_emergency'  => 'Personal emergency',
                'other'               => 'Other',
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data'   => $reasons
        ]);
    }

    public function decide(Request $request, $orderId)
    {
        $request->validate([
            'decision'         => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:decision,reject|in:vehicle_breakdown,vehicle_accident,traffic_jam,health_emergency,personal_emergency,other',

        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $deliveryPerson = $user->deliveryPerson;

            if (!$deliveryPerson) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'You are not a delivery person.'
                ], 403);
            }

            $delivery = Delivery::where('delivery_person_id', $deliveryPerson->id)
                ->whereHas('order', fn($q) => $q->where('id', $orderId))
                ->first();

            if (!$delivery) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Order not found or not assigned to you.'
                ], 404);
            }

            if (!is_null($delivery->acceptance_status)) {
                DB::rollBack();
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Decision has already been taken for this delivery.'
                ], 409);
            }

            if ($request->decision === 'approve') {
                $delivery->acceptance_status = 1;
                $delivery->rejection_reason  = null;
                $delivery->status            = 'accepted';



            } else {
                $delivery->acceptance_status = 0;
                $delivery->rejection_reason  = $request->rejection_reason;
                $delivery->status            = 'rejection';
                //$delivery->delivery_person_id = null;
            }

            $delivery->save();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => $request->decision === 'approve'
                    ? 'Delivery approved successfully.'
                    : 'Delivery rejected successfully.',
               // 'data'    => $delivery
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to take decision.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getDeliveryPerson($orderId)
    {
        $user = Auth::user();

        $order = Order::with('delivery.deliveryPerson')
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found.',
            ], 404);
        }


        if (!$order->delivery) {
            return response()->json([
                'status'  => false,
                'message' => 'No delivery assigned to this order yet.',
            ], 404);
        }


        if (!$order->delivery->deliveryPerson) {
            return response()->json([
                'status'  => false,
                'message' => 'Delivery person not assigned yet.',
            ], 404);
        }


        if ($order->delivery->status !== 'on_the_way') {
            return response()->json([
                'status'  => false,
                'message' => 'Delivery person details will be available once the order is on the way.',
                'current_status' => $order->delivery->status
            ], 403);
        }

        $deliveryPerson = $order->delivery->deliveryPerson->user;


        return response()->json([
            'status' => true,
            'message' => 'Delivery person details retrieved successfully.',
            'data' => [
                'id'     => $deliveryPerson->id,
                'name'   => $deliveryPerson->name,
                'phone'  => $deliveryPerson->phone,
                'status' => $order->delivery->status,
            ]
        ]);
    }










}
