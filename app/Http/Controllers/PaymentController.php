<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;
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

}
