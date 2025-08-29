<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;


class PaymentController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function payPrepayment($id)
    {
        $user = Auth::user();

        $order = Order::with('orderDetails.package')->where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }

        if (!$order->is_approved || $order->status !== 'confirmed') {
            return response()->json(['status' => false, 'message' => 'Order is not approved yet.'], 403);
        }

        if ($order->prepayment_paid) {
            return response()->json(['status' => false, 'message' => 'Prepayment already made.'], 400);
        }

        $prepaymentAmount = 0;
        foreach ($order->orderDetails as $detail) {
            $package = $detail->package;
            if ($package->prepayment_required) {
                $prepaymentAmount += ($order->total_price * ($package->prepayment_amount / 100));
            }
        }

        if ($prepaymentAmount <= 0) {
            return response()->json(['status' => false, 'message' => 'No prepayment required.'], 400);
        }

        $intent = $this->stripe->createPrepaymentIntent($prepaymentAmount);

        return response()->json([
            'status' => true,
            'message' => 'Prepayment intent created.',
            'client_secret' => $intent->client_secret,
        ]);
    }


// test
    public function createPaymentIntent()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => 1000,
                'currency' => 'usd',
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

//////////////////
    public function createIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $amountInCents = (int) round($request->amount * 100);

            $intent = \Stripe\PaymentIntent::create([
                'amount'   => $amountInCents,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            return response()->json([
                'status'        => true,
                'client_secret' => $intent->client_secret,
                'amount'        => $request->amount,
            ]);
        } catch (\Exception $e) {
            Log::error('Stripe createIntent error: '.$e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Failed to create payment intent',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    public function payBill(Request $request, $billId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:partial,full',
            'payment_method' => 'required|in:electronic,cash',
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::with('order')->find($billId);

            if (!$bill) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bill not found.',
                ], 404);
            }

            if ($bill->status === 'paid') {
                return response()->json([
                    'status' => false,
                    'message' => 'Bill already fully paid.',
                ], 400);
            }


            if ($request->payment_method === 'cash') {
                return response()->json([
                    'status' => false,
                    'message' => 'Please visit the restaurant to complete your cash payment.',
                ], 200);
            }


            $amount = $request->amount;
            $paymentType = $request->payment_type;

            if ($amount > $bill->amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment amount exceeds bill amount.',
                ], 400);
            }

            if ($paymentType === 'full') {
                $bill->status = 'paid';
                $bill->amount = 0;

                $bill->order->final_payment_paid = true;
                $bill->order->final_payment_paid_at = now();
                $bill->order->save();

            } elseif ($paymentType === 'partial') {
                $bill->amount -= $amount;
                $bill->status = $bill->amount > 0 ? 'partially_paid' : 'paid';

                $bill->order->prepayment_paid = true;
                $bill->order->prepayment_paid_at = now();
                $bill->order->save();
            }

            $bill->save();

            Payment::create([
                'bill_id' => $bill->id,
                'user_id' => $bill->user_id,
                'payment_method' => $request->payment_method,
                'amount' => $amount,
                'payment_status' => 'completed',
                'paid_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment processed successfully.',
                'data' => [
                    'bill_id' => $bill->id,
                    'remaining_amount' => $bill->amount,
                    'status' => $bill->status,
                    'payment_type' => $paymentType,
                    'payment_method' => $request->payment_method,
                ]
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Payment failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



}
