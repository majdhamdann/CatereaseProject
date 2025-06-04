<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchManagementController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;



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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verify']);


Route::middleware(['auth:sanctum', 'manager'])->group(function () {
  Route::get('earnings/{branch_id}',[DashboardController::class,'getBranchProfit']);
  Route::get('Order/{branch_id}',[DashboardController::class,'getLatestDeliveredOrders']);
  Route::get('orders/{branch_id}/monthly-stats',[DashboardController::class,'getMonthlyDeliveredOrders']);
  Route::get('orders/{branch_id}/numberallstatus',[DashboardController::class,'getOrderStatusCounts']);
  Route::get('orders/{branch_id}/everymonth',[DashboardController::class,'getMonthlyStatusBreakdown']);
  Route::get('/orders/delivered-category-stats/{branch_id}', [DashboardController::class, 'getDeliveredCategoryStats']);
  Route::get('orders/statistics/popular-food-categories/{branch_id}', [DashboardController::class, 'getPopularCategoriesByUsers']);


});
  Route::get('/allbranch', [BranchManagementController::class, 'getAllBranchesWithDetails']);
Route::get('/restaurants', [RestaurantController::class, 'index']);
