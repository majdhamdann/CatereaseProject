<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login',[AuthController::class,'login']);

Route::middleware(['auth:sanctum', 'manager'])->group(function () {
  Route::get('earnings/{branch_id}',[ManagerController::class,'getBranchProfit']);
  Route::get('Order/{branch_id}',[ManagerController::class,'getLatestDeliveredOrders']);
  Route::get('orders/{branch_id}/monthly-stats',[ManagerController::class,'getMonthlyDeliveredOrders']);
  Route::get('orders/{branch_id}/numberallstatus',[ManagerController::class,'getOrderStatusCounts']);
  Route::get('orders/{branch_id}/everymonth',[ManagerController::class,'getMonthlyStatusBreakdown']);
  Route::get('/orders/delivered-category-stats/{branch_id}', [ManagerController::class, 'getDeliveredCategoryStats']);
  Route::get('orders/statistics/popular-food-categories/{branch_id}', [ManagerController::class, 'getPopularCategoriesByUsers']);


});