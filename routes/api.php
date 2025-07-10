<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodManagementController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Manager\FoodItemController;
use App\Http\Controllers\MenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AddressController;

use App\Http\Controllers\PackageController;

use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Manager\MenuManagementController;
use App\Http\Controllers\Owner\BranchController as OwnerBranchController;
use App\Http\Controllers\Owner\WorkingDayController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'customer'])->prefix('customer')->group(function () {
    Route::get('/show', [CustomerController::class, 'show']);
    Route::post('/update', [CustomerController::class, 'update']);
    Route::post('/update-password', [CustomerController::class, 'updatePassword']);
    Route::post('/creat', [AddressController::class, 'store']);
    Route::get('/list-addresses', [AddressController::class, 'index']);
    Route::post('/update_addresse/{id}', [AddressController::class, 'update']);
    Route::delete('/delete_addresse/{id}', [AddressController::class, 'delete']);
    Route::post('/addresses/{id}/default', [AddressController::class, 'setDefault']);
});

//Branch
Route::prefix('branches')->group(function () {
    Route::get('/nearby', [BranchController::class, 'getNearby']);
});

Route::post('login',[AuthController::class,'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verify']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendResetOtp']);

Route::post('/forgot-password/reset', [AuthController::class, 'resetPasswordAfterVerification']);

Route::get('/categories', [MenuManagementController::class, 'allCategory']);

Route::middleware(['auth:sanctum', 'manager'])->group(function () {
     Route::get('/mybranch',[DashboardController::class,'getMyBranch']);
    Route::get('earnings/{branch_id}',[DashboardController::class,'getBranchProfit']);
    Route::get('Order/{branch_id}',[DashboardController::class,'getLatestDeliveredOrders']);
    Route::get('orders/{branch_id}/monthly-stats',[DashboardController::class,'getMonthlyDeliveredOrders']);
    Route::get('orders/{branch_id}/numberallstatus',[DashboardController::class,'getOrderStatusCounts']);
    Route::get('orders/{branch_id}/everymonth',[DashboardController::class,'getMonthlyStatusBreakdown']);
    Route::get('/orders/delivered-category-stats/{branch_id}', [DashboardController::class, 'getDeliveredCategoryStats']);
    Route::get('orders/statistics/popular-food-categories/{branch_id}', [DashboardController::class, 'getPopularCategoriesByUsers']);
   // Route::apiResource('food-items', FoodItemController::class);
      Route::apiResource('food-items', FoodManagementController::class);
      Route::get('/branches/categories', [MenuManagementController::class, 'indexForManager']);


});
Route::middleware(['auth:sanctum', 'admin_or_owner'])->group(function () {
   Route::apiResource('users', UserManagementController::class);
    Route::apiResource('/branches', OwnerBranchController::class);
    Route::get('/branches/{branchId}/working-days', [WorkingDayController::class, 'index']);
    Route::post('/branches/{branchId}/working-days', [WorkingDayController::class, 'store']);
    Route::put('/working-days/{id}', [WorkingDayController::class, 'update']);
    Route::delete('/working-days/{id}', [WorkingDayController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::get('/role', [UserManagementController::class, 'allRole']);
    Route::apiResource('restaurants', AdminRestaurantController::class);
    Route::apiResource('complaints', \App\Http\Controllers\Admin\ComplaintController::class);

});
Route::middleware(['auth:sanctum','owner'])->prefix('owner')->group(function () {
    Route::get('/my-restaurant', [OwnerBranchController::class, 'showRestaurantDetails']);
    Route::post('/branches/{branch}/categories', [OwnerBranchController::class, 'addCategoriesToBranch']);

});

Route::get('/allbranch', [BranchController::class, 'getAllBranchesWithDetails']);
Route::get('/restaurant', [RestaurantController::class, 'index']);
//Route::get('/restaurants/category/{name}', [RestaurantController::class, 'getByCategory']);
Route::get('/menu/items', [MenuController::class, 'filterFoodItems']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/branches/category/{categoryName}', [BranchController::class, 'getBranchesByCategoryName']);
    Route::get('/branches/{branch}/food-items', [BranchController::class, 'getItems']);
});

// delivery routes
Route::middleware(['auth:sanctum', 'delivery'])->prefix('delivery')->group(function () {
    Route::get('/orders', [DeliveryController::class, 'assignedOrders']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/branches/{branch}/packages', [PackageController::class, 'index']);
    Route::get('packages/{id}', [PackageController::class, 'show']);
    Route::get('/packages', [PackageController::class, 'listPackages']);
});



