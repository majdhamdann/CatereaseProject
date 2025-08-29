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


    public function makePayment(Request $request, $billId)
    {
        $request->validate([
            'payment_type'   => 'required|in:partial,full',
            'payment_method' => 'required|string',
            'amount'         => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $bill = Bill::with('order')->find($billId);

            if (!$bill) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Bill not found.',
                ], 404);
            }

            if ($bill->status === 'paid') {
                return response()->json([
                    'status'  => false,
                    'message' => 'Bill already paid in full.',
                ], 400);
            }

            $userId = Auth::id();
            $amount = $request->amount;


            $payment = Payment::create([
                'bill_id'        => $bill->id,
                'user_id'        => $userId,
                'payment_method' => $request->payment_method,
                'amount'         => $amount,
                'payment_status' => 'completed',
                'paid_at'        => now(),
            ]);


            $totalPaid = Payment::where('bill_id', $bill->id)
                ->where('payment_status', 'completed')
                ->sum('amount');


            if ($totalPaid >= $bill->amount) {
                $bill->status = 'paid';
            } elseif ($totalPaid > 0) {
                $bill->status = 'partially_paid';
            } else {
                $bill->status = 'unpaid';
            }

            $bill->save();


            if ($bill->order) {
                if ($request->payment_type === 'partial') {
                    $bill->order->prepayment_paid = true;
                    $bill->order->prepayment_paid_at = now();
                } elseif ($request->payment_type === 'full') {
                    $bill->order->final_payment_paid = true;
                    $bill->order->final_payment_paid_at = now();
                }
                $bill->order->save();
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Payment recorded successfully.',
                'data'    => [
                    'bill_id'     => $bill->id,
                    'bill_amount' => $bill->amount,
                    'total_paid'  => $totalPaid,
                    'bill_status' => $bill->status,
                    'payment'     => $payment,
                ]
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Payment failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


}
