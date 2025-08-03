<?php

use Illuminate\Support\Facades\Route;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Services\StripeService;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    try {
        $stripe = new StripeService();
        $intent = $stripe->createPrepaymentIntent(10.00);

        return response()->json([
            'success' => true,
            'client_secret' => $intent->client_secret
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/l', function () {
    return view('welcome');
});
