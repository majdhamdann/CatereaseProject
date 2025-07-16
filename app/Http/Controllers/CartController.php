<?php

namespace App\Http\Controllers;

use App\Models\OccasionType;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartPackageExtra;
use App\Models\Package;
use App\Models\PackageExtra;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id'         => 'required|exists:packages,id',
            'quantity'           => 'required|integer|min:1',
            'extra_persons'      => 'required|integer|min:0',
            'service_type'       => 'sometimes|array',
            'service_type.*.id'  => 'required|exists:branch_service_types,id',
            'occasion_type_id'   => 'sometimes|exists:occasion_types,id',
            'extras'             => 'sometimes|array',
            'extras.*.extra_id'  => 'required|exists:package_extras,id',
            'extras.*.quantity'  => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $package = Package::with(['extras', 'extraServices', 'discounts'])->find($request->package_id);

        if ($request->extra_persons > $package->max_extra_persons) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot add more than ' . $package->max_extra_persons . ' extra persons.'
            ], 422);
        }

        $activeDiscount = $package->discounts()
            ->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now())
            ->first();

        $originalPrice = (float) $package->base_price;
        $discountPercentage = $activeDiscount ? (float) $activeDiscount->value : 0;
        $discountedPrice = $activeDiscount
            ? max($originalPrice * (1 - ($discountPercentage / 100)), 0)
            : $originalPrice;

        $baseCost = $discountedPrice * $request->quantity;
        $extraPersonsCost = $request->extra_persons * (float) $package->price_per_extra_person;

        $extrasCost = 0;
        $extrasDetails = [];

        foreach ($request->input('extras', []) as $ex) {
            $extraItem = PackageExtra::findOrFail($ex['extra_id']);
            $qty = $ex['quantity'];
            $unitPrice = (float) $extraItem->price;
            $total = $unitPrice * $qty;
            $extrasCost += $total;

            $extrasDetails[] = [
                'id'       => $extraItem->id,
                'name'     => $extraItem->name,
                'price'    => $unitPrice,
                'quantity' => $qty,
                'total'    => $total,
            ];
        }

        $serviceTypesCost = 0;
        $serviceTypesDetails = [];

        foreach ($request->input('service_type', []) as $service) {
            $serviceModel = \App\Models\BranchServiceType::with('serviceType')->findOrFail($service['id']);
            $price = (float) $serviceModel->service_cost;
            $serviceTypesCost += $price;

            $serviceTypesDetails[] = [
                'id'    => $serviceModel->id,
                'name'  => $serviceModel->serviceType->name ?? 'Unknown',
                'price' => $price,
            ];
        }

        $totalCost = $baseCost + $extraPersonsCost + $extrasCost + $serviceTypesCost;

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::create([
            'cart_id'          => $cart->id,
            'package_id'       => $package->id,
            'quantity'         => $request->quantity,
            'extra_persons'    => $request->extra_persons,
            'occasion_type_id' => $request->occasion_type_id,
            'total_price'      => $totalCost
        ]);

        foreach ($request->input('extras', []) as $ex) {
            $extraItem = PackageExtra::findOrFail($ex['extra_id']);
            CartPackageExtra::create([
                'cart_item_id' => $cartItem->id,
                'extra_id'     => $ex['extra_id'],
                'quantity'     => $ex['quantity'],
                'unit_price'   => $extraItem->price,
                'total_price'  => $extraItem->price * $ex['quantity']
            ]);
        }

        foreach ($request->input('service_type', []) as $service) {
            $serviceModel = \App\Models\BranchServiceType::findOrFail($service['id']);
            $cartItem->services()->attach($service['id'], [
                'custom_price' => $serviceModel->service_cost
            ]);
        }


        $cart->total_price = $cart->items()->sum('total_price');
        $cart->save();

        $occasionName = null;
        if ($request->occasion_type_id) {
            $occasion = OccasionType::find($request->occasion_type_id);
            $occasionName = $occasion->name ?? null;
        }


        $discountDetails = [];
        if ($activeDiscount) {
            $discountDetails = [
                'discount_percentage' => number_format($discountPercentage, 0) . '%',
                'discounted_price'    => number_format($discountedPrice, 2),
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Package successfully added to cart.',
            'data' => [
                'cart_item_id' => $cartItem->id,
                'total_price'  => $totalCost,
                'cart_total'   => $cart->total_price,
                'details' => array_merge([
                    'package'             => $package->name,
                    'original_price'      => number_format($originalPrice, 2),
                ], $discountDetails, [
                    'quantity'            => $request->quantity,
                    'extra_persons'       => $request->extra_persons,
                    'extra_persons_cost'  => number_format($extraPersonsCost, 2),
                    'extras'              => $extrasDetails,
                    'service_type'        => $serviceTypesDetails,
                    'service_type_cost'   => number_format($serviceTypesCost, 2),
                    'occasion_type_id'    => $request->occasion_type_id,
                    'occasion_type_name'  => $occasionName,
                    'total'               => number_format($totalCost, 2),
                ])
            ]
        ]);
    }

    public function updateCartItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|integer|min:1',
            'extra_persons' => 'sometimes|integer|min:0',
            'occasion_type_id' => 'sometimes|exists:occasion_types,id',
            'service_type' => 'sometimes|array',
            'service_type.*.id' => 'required|exists:branch_service_types,id',
            'extras' => 'sometimes|array',
            'extras.*.extra_id' => 'required|exists:package_extras,id',
            'extras.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $cartItem = CartItem::with(['package', 'cart', 'packageExtras.extra', 'services'])->findOrFail($id);

            if ($cartItem->cart->user_id !== Auth::id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized action'
                ], 403);
            }

            $package = $cartItem->package;
            $originalTotal = $cartItem->total_price;


            $cartItem->quantity = $request->input('quantity', $cartItem->quantity);
            $cartItem->extra_persons = $request->input('extra_persons', $cartItem->extra_persons);
            $cartItem->occasion_type_id = $request->input('occasion_type_id', $cartItem->occasion_type_id);

            if ($cartItem->extra_persons > $package->max_extra_persons) {
                throw new \Exception('You cannot add more than ' . $package->max_extra_persons . ' extra persons.');
            }


            $activeDiscount = $package->discounts()
                ->where('is_active', true)
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->first();

            $originalPrice = (float) $package->base_price;
            $discountPercentage = $activeDiscount ? (float) $activeDiscount->value : 0;
            $discountedPrice = $originalPrice * (1 - ($discountPercentage / 100));
            $discountedPrice = max($discountedPrice, 0);

            $baseCost = $discountedPrice * $cartItem->quantity;
            $extraPersonsCost = $cartItem->extra_persons * (float) $package->price_per_extra_person;


            $extrasCost = 0;
            $existingExtras = $cartItem->packageExtras->keyBy('extra_id');
            $newExtraIds = [];
            $extrasDetails = [];

            foreach ($request->input('extras', []) as $ex) {
                $extraModel = PackageExtra::findOrFail($ex['extra_id']);
                $total = $extraModel->price * $ex['quantity'];
                $extrasCost += $total;
                $newExtraIds[] = $ex['extra_id'];

                if (isset($existingExtras[$ex['extra_id']])) {
                    $existingExtras[$ex['extra_id']]->update([
                        'quantity' => $ex['quantity'],
                        'total_price' => $total
                    ]);
                } else {
                    CartPackageExtra::create([
                        'cart_item_id' => $cartItem->id,
                        'extra_id' => $ex['extra_id'],
                        'quantity' => $ex['quantity'],
                        'unit_price' => $extraModel->price,
                        'total_price' => $total
                    ]);
                }

                $extrasDetails[] = [
                    'id' => $extraModel->id,
                    'name' => $extraModel->name,
                    'price' => $extraModel->price,
                    'quantity' => $ex['quantity'],
                    'total' => $total
                ];
            }


            $cartItem->packageExtras()->whereNotIn('extra_id', $newExtraIds)->delete();


            $serviceTypesCost = 0;
            $serviceTypesDetails = [];
            $cartItem->services()->detach();

            foreach ($request->input('service_type', []) as $service) {
                $serviceModel = \App\Models\BranchServiceType::with('serviceType')->findOrFail($service['id']);
                $price = (float) $serviceModel->service_cost;
                $serviceTypesCost += $price;

                $cartItem->services()->attach($service['id'], [
                    'custom_price' => $price
                ]);

                $serviceTypesDetails[] = [
                    'id' => $serviceModel->id,
                    'name' => $serviceModel->serviceType->name ?? 'Unknown',
                    'price' => $price,
                ];
            }


            $totalCost = $baseCost + $extraPersonsCost + $extrasCost + $serviceTypesCost;
            $cartItem->total_price = $totalCost;
            $cartItem->save();


            $cart = $cartItem->cart;
            $cart->total_price = $cart->items()->sum('total_price');
            $cart->save();

            DB::commit();


            $occasionName = null;
            if ($cartItem->occasion_type_id) {
                $occasion = OccasionType::find($cartItem->occasion_type_id);
                $occasionName = $occasion->name ?? null;
            }


            $details = [
                'package' => $package->name,
                'quantity' => $cartItem->quantity,
                'extra_persons' => $cartItem->extra_persons,
                'extra_persons_cost' => number_format($extraPersonsCost, 2),
                'extras' => $extrasDetails,
                'service_type' => $serviceTypesDetails,
                'service_type_cost' => number_format($serviceTypesCost, 2),
                'occasion_type_id' => $cartItem->occasion_type_id,
                'occasion_type_name' => $occasionName,
                'total' => number_format($totalCost, 2),
            ];

            if ($discountPercentage > 0) {
                $details['original_price'] = number_format($originalPrice, 2);
                $details['discount_percentage'] = number_format($discountPercentage, 0) . '%';
                $details['discounted_price'] = number_format($discountedPrice, 2);
            }

            return response()->json([
                'status' => true,
                'message' => 'Cart item updated successfully.',
                'data' => [
                    'cart_item_id' => $cartItem->id,
                    'total_price' => $cartItem->total_price,
                    'cart_total' => $cart->total_price,
                    'details' => $details
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCartPackages(Request $request)
    {

        $cartItems = CartItem::with('package')
            ->where('cart_id', auth()->user()->cart->id)
            ->get();

        $packages = $cartItems->map(function ($item) {
            return [
                'cart_item_id' => $item->id,
                'id' => $item->package->id,
                'name' => $item->package->name,
                'photo' => $item->package->photo,
                'description' => $item->package->description,
                'serves_count' => $item->package->serves_count,
                'base_price' => $item->package->base_price,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Cart packages retrieved successfully.',
            'packages' => $packages,

        ]);
    }

    public function showCartItem($cartItemId)
    {
        $user = Auth::user();

        try {

            $cartItem = CartItem::with([
                'cart' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
                'package.branch.restaurant',
                'package.extraServices.serviceType',
                'package.branchServiceType.serviceType',
                'package.occasionTypes',
                'package.categories',
                'package.items.foodItem',
                'package.extras.foodItem',
                'package.extras.branchServiceType.serviceType',
                'package.discounts',
                'occasionType',
                'extras.extra',
                'services.serviceType'
            ])->findOrFail($cartItemId);

            if (!$cartItem->cart) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart item not found'
                ], 404);
            }

            $package = $cartItem->package;


            $allServices = collect();


            if ($package->branchServiceType) {
                $allServices->push([
                    'id' => $package->branchServiceType->id,
                    'name' => $package->branchServiceType->serviceType->name ?? null,
                    'custom_price' => $package->branchServiceType->custom_price
                ]);
            }


            foreach ($package->extraServices as $service) {
                $allServices->push([
                    'id' => $service->id,
                    'name' => $service->serviceType->name ?? null,
                    'custom_price' => $service->custom_price
                ]);
            }


            $now = now();
            $currentDiscount = $package->discounts
                ->where('is_active', true)
                ->where('start_at', '<=', $now)
                ->where('end_at', '>=', $now)
                ->first();


            $formattedItem = [
                'package_id' => $package->id,
                'name' => $package->name,
                'description' => $package->description,
                'serves_count' => $package->serves_count,
                'photo' => $package->photo,
                'base_price' => number_format($package->base_price, 2, '.', ''),
                'prepayment_required' => $package->prepayment_required,
                'prepayment_amount' => $package->prepayment_amount,
                'branch_id' => $package->branch_id,
                'branch_name' => $package->branch->name ?? ($package->branch->restaurant->name ?? 'Unknown'),
                'service_type' => $allServices->toArray(),
                'occasion_types' => $package->occasionTypes->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name
                    ];
                })->toArray(),
                'categories' => $package->categories->pluck('name')->toArray(),
                'max_extra_persons' => $package->max_extra_persons,
                'price_per_extra_person' => $package->price_per_extra_person,
                'items' => $package->items->map(function ($item) {
                    return [
                        'food_item_id' => $item->food_item_id,
                        'food_item_name' => $item->foodItem->name ?? null,
                        'quantity' => $item->quantity,
                        'is_optional' => $item->is_optional
                    ];
                })->toArray(),
                'extras' => $package->extras->map(function ($extra) {
                    $extraName = $extra->name;
                    if ($extra->type === 'food_item' && $extra->foodItem) {
                        $extraName = $extra->foodItem->name;
                    } elseif ($extra->type === 'service' && $extra->branchServiceType && $extra->branchServiceType->serviceType) {
                        $extraName = $extra->branchServiceType->serviceType->name;
                    }
                    return [
                        'id' => $extra->id,
                        'type' => $extra->type,
                        'name' => $extraName,
                        'price' => $extra->price,
                        'is_optional' => $extra->is_optional
                    ];
                })->toArray(),


                'selected_options' => [
                    'quantity' => $cartItem->quantity,
                    'extra_persons' => $cartItem->extra_persons,
                    'occasion_type' => $cartItem->occasionType ? [
                        'id' => $cartItem->occasionType->id,
                        'name' => $cartItem->occasionType->name
                    ] : null,
                    'selected_extras' => $cartItem->extras->map(function ($extra) {
                        return [
                            'id' => $extra->extra->id,
                            'name' => $extra->extra->name,
                            'price' => $extra->unit_price,
                            'quantity' => $extra->quantity,
                            'total' => $extra->total_price
                        ];
                    })->toArray(),
                    'selected_services' => $cartItem->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'name' => $service->serviceType->name,
                            'price' => $service->pivot->custom_price
                        ];
                    })->toArray()
                ]
            ];


            if ($currentDiscount) {
                $discountValue = $currentDiscount->value;
                $discountedPrice = round($package->base_price - ($package->base_price * ($discountValue / 100)), 2);

                $formattedItem['discount'] = [
                    'value' => $discountValue . '%',
                    'description' => $currentDiscount->description,
                    'start_at' => $currentDiscount->start_at,
                    'end_at' => $currentDiscount->end_at,
                ];

                $formattedItem['final_price'] = number_format($discountedPrice, 2, '.', '');
            }

            return response()->json([
                'status' => 'success',
                'data' => $formattedItem
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found'
            ], 404);
        }
    }
    
    public function removeCartItem($cartItemId)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();


            $cartItem = CartItem::whereHas('cart', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->findOrFail($cartItemId);


            $itemPrice = $cartItem->total_price;


            $cartItem->packageExtras()->delete();
            $cartItem->services()->detach();


            $cartItem->delete();


            $cart = $cartItem->cart;
            $cart->total_price = $cart->items()->sum('total_price');
            $cart->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'package removed from cart successfully.',
                'cart_total' => number_format($cart->total_price, 2)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Cart package not found'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove package from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
