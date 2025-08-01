<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PackageExtra;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class OrderController extends Controller
{
    public function initOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id'     => 'nullable|exists:addresses,id',
            'street'         => 'required_without:address_id|string',
            'building'       => 'required_without:address_id|string',
            'floor'          => 'nullable|string',
            'apartment'      => 'nullable|string',
            'city_id'        => 'required_without:address_id|exists:cities,id',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'notes'          => 'nullable|string',
            'delivery_time'  => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        if ($request->address_id) {

            $address = Address::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$address) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Address not found or unauthorized.'
                ], 404);
            }
        } else {

            $address = Address::create([
                'user_id'   => $user->id,
                'city_id'   => $request->city_id,
                'street'    => $request->street,
                'building'  => $request->building,
                'floor'     => $request->floor,
                'apartment' => $request->apartment,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
                'is_default'=> false,
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Address is ready for order.',
            'data'    => [
                'address_id'     => $address->id,
                'notes'          => $request->notes,
                'delivery_time'  => $request->delivery_time,
            ]
        ]);
    }

    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_item_ids'   => 'required|array|min:1',
            'cart_item_ids.*' => 'exists:cart_items,id',
            'address_id'      => 'required|exists:addresses,id',
            'notes'           => 'nullable|string',
            'delivery_time'   => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $cart = $user->cart;

        $cartItems = CartItem::with([
            'package.discounts',
            'extras.extra',
            'services',
            'occasionType'
        ])->whereIn('id', $request->cart_item_ids)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No valid items found in cart.'
            ], 404);
        }

        DB::beginTransaction();

        try {
            $createdOrderIds = [];

            foreach ($cartItems as $item) {
                $package = $item->package;
                $discount = $package->discounts()
                    ->where('is_active', true)
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>=', now())
                    ->first();

                $unitPrice = $package->base_price;
                if ($discount) {
                    $unitPrice -= ($unitPrice * ($discount->value / 100));
                }

                $order = Order::create([
                    'user_id'           => $user->id,
                    'branch_id'         => $package->branch_id,
                    'address_id'        => $request->address_id,
                    'cart_id'           => $cart->id,
                    'status'            => 'pending',
                    'is_approved'       => false,
                     // 'approval_deadline' => now()->addHours(3),
                    //'approval_deadline' => now()->addDays(1)->addHours(4),
                    'notes'             => $request->notes,
                    'delivery_time'     => $request->delivery_time,
                    'total_price'       => 0
                ]);

                $createdOrderIds[] = $order->id;

                $orderDetail = OrderDetail::create([
                    'order_id'          => $order->id,
                    'package_id'        => $package->id,
                    'extra_persons'     => $item->extra_persons,
                    'occasion_type_id'  => $item->occasionType?->id,
                    'quantity'          => $item->quantity,
                    'unit_price'        => $unitPrice
                ]);

                $total = $unitPrice * $item->quantity;
                $extraPersonsCost = $item->extra_persons * $package->price_per_extra_person;
                $total += $extraPersonsCost;

                foreach ($item->services as $service) {
                    \App\Models\OrderItemService::create([
                        'order_detail_id'        => $orderDetail->id,
                        'branch_service_type_id' => $service->id,
                        'custom_price'           => $service->pivot->custom_price,
                    ]);
                    $total += $service->pivot->custom_price;
                }

                foreach ($item->extras as $extra) {
                    DB::table('order_package_extras')->insert([
                        'order_detail_id' => $orderDetail->id,
                        'extra_id'        => $extra->extra_id,
                        'quantity'        => $extra->quantity,
                        'unit_price'      => $extra->unit_price,
                        'total_price'     => $extra->total_price,
                        'created_at'      => now(),
                        'updated_at'      => now()
                    ]);
                    $total += $extra->total_price;
                }

                $order->update(['total_price' => $total]);

                $order->bill()->create([
                    'user_id'   => $user->id,
                    'amount'    => $total,
                    'issued_at' => now(),
                ]);

                $item->delete();
            }

            $cart->total_price = $cart->items()->sum('total_price');
            $cart->save();

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Orders created successfully.',
                'orders' => collect($createdOrderIds)->map(fn($id) => ['order_id' => $id])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create orders.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function listUserOrders()
    {
        $user = Auth::user();

        $orders = Order::with([
            'orderDetails.package.branch',
            'orderDetails.package.discounts' => function ($query) {
                $query->where('is_active', true)
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>=', now());
            }
        ])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'order_id'      => $order->id,
                    'status'        => $order->status,
                    'delivery_time' => $order->delivery_time,
                    'packages'      => $order->orderDetails->map(function ($detail) {
                        $package = $detail->package;

                        $discount = $package->discounts->first();

                        $data = [
                            'package_id'   => $package->id,
                            'name'         => $package->name,
                            'photo'        => $package->photo,
                            'branch_name'  => $package->branch->name ?? ($package->branch->restaurant->name ?? 'Unknown'),
                            'base_price'   => number_format($package->base_price, 2),
                        ];

                        if ($discount) {
                            $discounted = $package->base_price - ($package->base_price * $discount->value / 100);
                            $data['discount'] = [
                                'value'            => $discount->value . '%',
                                'discounted_price' => number_format($discounted, 2),
                            ];
                        }

                        return $data;
                    }),
                ];
            });

        return response()->json([
            'status'  => true,
            'message' => 'User orders retrieved successfully.',
            'data'    => $orders
        ]);
    }

    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'delivery_time'  => 'required|date|after:now',
            'notes'          => 'nullable|string|max:1000',

            'packages' => 'required|array|min:1',
            'packages.*.order_detail_id' => 'required|exists:order_details,id',
            'packages.*.quantity' => 'required|integer|min:1',
            'packages.*.extra_persons' => 'required|integer|min:0',
            'packages.*.occasion_type_id' => 'required|exists:occasion_types,id',

            'packages.*.services' => 'nullable|array',
            'packages.*.services.*.branch_service_type_id' => 'required|exists:branch_service_types,id',

            'packages.*.extras' => 'nullable|array',
            'packages.*.extras.*.extra_id' => 'required|exists:package_extras,id',
            'packages.*.extras.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::user();

            $order = Order::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

            $order->update([
                'address_id'    => $request->address_id,
                'delivery_time' => $request->delivery_time,
                'notes'         => $request->notes,
            ]);

            $totalOrderPrice = 0;

            foreach ($request->packages as $pkg) {
                $orderDetail = OrderDetail::with('package')->findOrFail($pkg['order_detail_id']);
                $package = $orderDetail->package;


                if ($pkg['extra_persons'] > $package->max_extra_persons) {
                    throw ValidationException::withMessages([
                        'packages' => ["The number of extra persons exceeds the allowed limit for package: {$package->name}."]
                    ]);
                }


                $allowedOccasions = $package->occasionTypes->pluck('id')->toArray();
                if (!in_array($pkg['occasion_type_id'], $allowedOccasions)) {
                    throw ValidationException::withMessages([
                        'packages' => ["Selected occasion is not allowed for package: {$package->name}."]
                    ]);
                }


                if (!empty($pkg['services'])) {
                    $allowedServiceIds = $package->extraServices->pluck('id')->toArray();
                    foreach ($pkg['services'] as $srv) {
                        if (!in_array($srv['branch_service_type_id'], $allowedServiceIds)) {
                            throw ValidationException::withMessages([
                                'packages' => ["One or more services are not valid for package: {$package->name}."]
                            ]);
                        }
                    }
                }


                if (!empty($pkg['extras'])) {
                    $allowedExtraIds = $package->extras->pluck('id')->toArray();
                    foreach ($pkg['extras'] as $extra) {
                        if (!in_array($extra['extra_id'], $allowedExtraIds)) {
                            throw ValidationException::withMessages([
                                'packages' => ["One or more extras are not valid for package: {$package->name}."]
                            ]);
                        }
                    }
                }

                $orderDetail->update([
                    'quantity'          => $pkg['quantity'],
                    'extra_persons'     => $pkg['extra_persons'],
                    'occasion_type_id'  => $pkg['occasion_type_id'],
                ]);

                $orderDetail->services()->delete();
                $orderDetail->extras()->delete();

                $servicesTotal = 0;
                if (!empty($pkg['services'])) {
                    foreach ($pkg['services'] as $srv) {
                        $serviceModel = \App\Models\BranchServiceType::find($srv['branch_service_type_id']);
                        $cost = $serviceModel->service_cost ?? 0;

                        $orderDetail->services()->create([
                            'branch_service_type_id' => $srv['branch_service_type_id'],
                            'custom_price' => $cost,
                        ]);

                        $servicesTotal += $cost;
                    }
                }

                $extrasTotal = 0;
                if (!empty($pkg['extras'])) {
                    foreach ($pkg['extras'] as $extra) {
                        $extraModel = \App\Models\PackageExtra::find($extra['extra_id']);
                        $unitPrice = $extraModel->price;
                        $total = $unitPrice * $extra['quantity'];

                        $orderDetail->extras()->create([
                            'extra_id'    => $extraModel->id,
                            'quantity'    => $extra['quantity'],
                            'unit_price'  => $unitPrice,
                            'total_price' => $total,
                        ]);

                        $extrasTotal += $total;
                    }
                }

                $discount = $package->discounts
                    ->where('is_active', true)
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>=', now())
                    ->first();

                $basePrice = $package->base_price;
                $discounted = $discount ? $basePrice * (1 - $discount->value / 100) : $basePrice;

                $baseCost = $discounted * $pkg['quantity'];
                $extraPersonsCost = $pkg['extra_persons'] * $package->price_per_extra_person;

                $finalPackageTotal = $baseCost + $extraPersonsCost + $servicesTotal + $extrasTotal;
                $totalOrderPrice += $finalPackageTotal;
            }

            $order->update(['total_price' => $totalOrderPrice]);

            $order->bill()->update([
                'user_id'   => $user->id,
                'amount'    => $totalOrderPrice,
                'issued_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Order updated and recalculated successfully.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();

        $order = Order::with([
            'address.city',
            'orderDetails.package.items.foodItem',
            'orderDetails.package.branch.restaurant',
            'orderDetails.package.discounts',
            'orderDetails.occasionType',
            'orderDetails.services.service.serviceType',
            'orderDetails.extras.extra',
            'bill'
        ])
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order not found.'
            ], 404);
        }

        $details = $order->orderDetails->map(function ($detail) {
            $package = $detail->package;

            $discount = $package->discounts
                ->where('is_active', true)
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->first();

            $originalBasePrice = $package->base_price;
            $discountedBasePrice = $discount
                ? $originalBasePrice * (1 - ($discount->value / 100))
                : $originalBasePrice;

            $baseCost = $discountedBasePrice * $detail->quantity;
            $extraPersonsCost = $detail->extra_persons * $package->price_per_extra_person;
            $extrasCost = $detail->extras->sum(fn($extra) => $extra->total_price);
            $servicesCost = $detail->services->sum(fn($service) => $service->custom_price);

            $finalTotal = $baseCost + $extraPersonsCost + $extrasCost + $servicesCost;

            $prepaymentAmount = null;
            if ($package->prepayment_required && $package->prepayment_amount) {
                $prepaymentAmount = number_format(($package->prepayment_amount / 100) * $finalTotal, 2);
            }

            $data = [
                'order_details_id' => $detail->id,
                'package_id'   => $package->id,
                'name'         => $package->name,
                'branch_id'    => $package->branch_id,
                'branch_name'  => $package->branch->name ?? ($package->branch->restaurant->name ?? 'Unknown'),
                'description'  => $package->description,
                'photo'        => $package->photo,
                'base_price'   => number_format($originalBasePrice, 2),
                'quantity'     => $detail->quantity,
                'serves_count' => $package->serves_count,
                'extra_persons' => $detail->extra_persons,
                'price_per_extra_person' => number_format($package->price_per_extra_person, 2),
                'extra_persons_cost' => number_format($extraPersonsCost, 2),
                'total_persons' => $package->serves_count + $detail->extra_persons,

                'prepayment_required'    => (bool) $package->prepayment_required,
                'prepayment_percentage'  => $package->prepayment_amount ? $package->prepayment_amount . '%' : null,
                'prepayment_amount'      => $prepaymentAmount,
                'cancellation_policy'    => $package->cancellation_policy,
                'occasion_type'          => $detail->occasionType?->name,

                'items' => $package->items->map(fn($item) => [
                    'food_item_id'   => $item->food_item_id,
                    'food_item_name' => $item->foodItem->name ?? null,
                    'quantity'       => $item->quantity,
                    'is_optional'    => $item->is_optional,
                ]),

                'services' => $detail->services->map(function ($service) {
                    return [
                        'name'         => $service->service->serviceType->name ?? null,
                        'custom_price' => number_format($service->custom_price, 2)
                    ];
                }),

                'extras' => $detail->extras->map(function ($extra) {
                    return [
                        'name'         => $extra->extra->name,
                        'type'         => $extra->extra->type,
                        'quantity'     => $extra->quantity,
                        'unit_price'   => number_format($extra->unit_price, 2),
                        'total_price'  => number_format($extra->total_price, 2),
                    ];
                }),

                'final_total' => number_format($finalTotal, 2),
            ];

            if ($discount) {
                $data['discount'] = [
                    'value'       => $discount->value . '%',
                    'description' => $discount->description,
                ];
                $data['discounted_price'] = number_format($discountedBasePrice, 2);
            }

            return $data;
        });

        return response()->json([
            'status' => true,
            'message' => 'Order details retrieved successfully.',
            'data' => [
                'order_id'         => $order->id,
                'status'           => $order->status,
                'is_approved'      => (bool) $order->is_approved,
                'approval_deadline'=> $order->approval_deadline,
                'notes'            => $order->notes,
                'delivery_time'    => $order->delivery_time,
                'total_price'      => number_format($order->total_price, 2),
                'address' => [
                    'street'    => $order->address->street,
                    'building'  => $order->address->building,
                    'floor'     => $order->address->floor,
                    'apartment' => $order->address->apartment,
                    'city'      => $order->address->city->name ?? null,
                ],
                'packages' => $details,
                'bill' => $order->bill ? [
                    'amount'    => number_format($order->bill->amount, 2),
                    'issued_at' => $order->bill->issued_at,
                ] : null,
            ]
        ]);
    }

    public function deleteOrder($id)
    {
        $user = Auth::user();

        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (in_array($order->status, ['completed', 'in_progress'])) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete order in its current state.',
            ], 400);
        }

        $order->orderDetails()->each(function ($detail) {
            $detail->services()->delete();
            $detail->extras()->delete();
            $detail->delete();
        });

        if ($order->bill) {
            $order->bill()->delete();
        }

        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }

    public function submitOrderToBranch($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found.',
                ], 404);
            }

            if ($order->is_submitted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order has already been submitted.',
                ], 400);
            }

            if (!$order->branch_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Branch not assigned to this order.',
                ], 400);
            }

            $now = now();
            $order->is_submitted = true;
            $order->submitted_at = $now;
            $order->approval_deadline = now()->addDays(1)->addHours(4);

            $order->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order submitted successfully to branch.',
                'approval_deadline' => $order->approval_deadline->toDateTimeString(),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to submit order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkOrderApprovalStatus($id)
    {
        $user = Auth::user();

        $order = Order::with('orderDetails.package')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if ($order->status === 'cancelled') {
            return response()->json([
                'status' => false,
                'message' => 'Order was rejected.',
                'rejection_reason' => $order->rejection_reason,
            ]);
        }

        if ($order->status === 'confirmed') {
            $prepaymentRequired = false;
            $prepaymentAmount = 0;

            foreach ($order->orderDetails as $detail) {
                $package = $detail->package;
                if ($package && $package->prepayment_required) {
                    $prepaymentRequired = true;
                    $prepaymentAmount += ($order->total_price * ($package->prepayment_amount / 100));
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Order approved. Please proceed to prepayment.',
                'prepayment_required' => $prepaymentRequired,
                'prepayment_amount' => round($prepaymentAmount, 2),
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Order is still under review.',
            'current_status' => $order->status,
        ]);
    }







}
