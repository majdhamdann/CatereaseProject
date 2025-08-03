<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        try {
            $stripeKey = config('services.stripe.secret');


            Log::info("Setting Stripe key: " . substr($stripeKey, 0, 12) . "...");

            Stripe::setApiKey($stripeKey);
        } catch (\Exception $e) {
            Log::error("Stripe initialization error: " . $e->getMessage());
            throw $e;
        }
    }

    public function createPrepaymentIntent($amount)
    {
        try {
            $amountInCents = round($amount * 100);
            Log::info("Creating payment intent for amount: {$amountInCents} cents");

            $intent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            Log::info("Payment intent created: " . $intent->id);
            return $intent;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error("Stripe API error: " . $e->getMessage());
            Log::error("Full error: " . $e->getError());
            throw $e;
        }
    }
}
