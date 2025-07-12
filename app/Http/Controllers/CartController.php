<?php

namespace App\Http\Controllers;

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
            'package_id' => 'required|exists:packages,id',
            'quantity' => 'required|integer|min:1',
            'extra_persons' => 'required|integer|min:0',
            'extras' => 'sometimes|array',
            'extras.*.extra_id' => 'required|exists:package_extras,id',
            'extras.*.quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $package = Package::with('extras')->find($request->package_id);

        if ($request->extra_persons > $package->max_extra_persons) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot add more than ' . $package->max_extra_persons . ' extra persons.'
            ], 422);
        }

        $baseCost = $package->base_price * $request->quantity;
        $extraPersonsCost = $request->extra_persons * $package->price_per_extra_person;
        $extrasCost = 0;
        $extrasDetails = [];

        foreach ($request->input('extras', []) as $ex) {
            $extraItem = PackageExtra::findOrFail($ex['extra_id']);
            $qty = $ex['quantity'];
            $unitPrice = $extraItem->price;
            $total = $unitPrice * $qty;
            $extrasCost += $total;

            $extrasDetails[] = [
                'id' => $extraItem->id,
                'name' => $extraItem->name,
                'price' => $unitPrice,
                'quantity' => $qty,
                'total' => $total,
            ];
        }

        $totalCost = $baseCost + $extraPersonsCost + $extrasCost;

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'package_id' => $package->id,
            'quantity' => $request->quantity,
            'extra_persons' => $request->extra_persons,
            'total_price' => $totalCost
        ]);

        foreach ($request->input('extras', []) as $ex) {
            $extraItem = PackageExtra::findOrFail($ex['extra_id']);
            CartPackageExtra::create([
                'cart_item_id' => $cartItem->id,
                'extra_id' => $ex['extra_id'],
                'quantity' => $ex['quantity'],
                'unit_price' => $extraItem->price,
                'total_price' => $extraItem->price * $ex['quantity']
            ]);
        }

        $cart->total_price = $cart->items()->sum('total_price');
        $cart->save();

        return response()->json([
            'status' => true,
            'message' => 'Package successfully added to cart.',
            'data' => [
                'cart_item_id' => $cartItem->id,
                'total_price' => $totalCost,
                'cart_total' => $cart->total_price,
                'details' => [
                    'package' => $package->name,
                    'base_price' => $package->base_price,
                    'quantity' => $request->quantity,
                    'extra_persons' => $request->extra_persons,
                    'extra_persons_cost' => $extraPersonsCost,
                    'extras' => $extrasDetails,
                    'total' => $totalCost
                ]
            ]
        ]);
    }

    public function updateCartItem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|integer|min:1',
            'extra_persons' => 'sometimes|integer|min:0',
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
            $cartItem = CartItem::with(['package', 'cart', 'packageExtras.extra'])
                ->findOrFail($id);


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


            if ($cartItem->extra_persons > $package->max_extra_persons) {
                throw new \Exception('You cannot add more than ' . $package->max_extra_persons . ' extra persons.');
            }


            $baseCost = $package->base_price * $cartItem->quantity;
            $extraPersonsCost = $cartItem->extra_persons * $package->price_per_extra_person;
            $extrasCost = 0;


            $existingExtras = $cartItem->packageExtras->keyBy('extra_id');
            $newExtraIds = [];

            foreach ($request->input('extras', []) as $ex) {
                $extraModel = PackageExtra::findOrFail($ex['extra_id']);
                $total = $extraModel->price * $ex['quantity'];
                $extrasCost += $total;
                $newExtraIds[] = $ex['extra_id'];

                if (isset($existingExtras[$ex['extra_id']])) {
                    // تحديث الإضافة الموجودة
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
            }


            $cartItem->packageExtras()
                ->whereNotIn('extra_id', $newExtraIds)
                ->delete();


            $totalCost = $baseCost + $extraPersonsCost + $extrasCost;
            $cartItem->total_price = $totalCost;
            $cartItem->save();


            $cart = $cartItem->cart;
            $cart->total_price = $cart->total_price - $originalTotal + $totalCost;
            $cart->save();

            DB::commit();


            $cartItem->load('packageExtras.extra');

            return response()->json([
                'status' => true,
                'message' => 'Cart item updated successfully.',
                'data' => [
                    'cart_item_id' => $cartItem->id,
                    'total_price' => $cartItem->total_price,
                    'cart_total' => $cart->total_price,
                    'details' => [
                        'base_cost' => $baseCost,
                        'extra_persons_cost' => $extraPersonsCost,
                        'extras_cost' => $extrasCost,
                        'extras' => $cartItem->packageExtras->map(function($extra) {
                            return [
                                'id' => $extra->extra_id,
                                'name' => $extra->extra->name,
                                'quantity' => $extra->quantity,
                                'unit_price' => $extra->unit_price,
                                'total_price' => $extra->total_price
                            ];
                        })
                    ]
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


}
