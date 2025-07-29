<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Manager\DeliveryEmployeeManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\OccasionTypeController;
use App\Http\Controllers\PackageController;

use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Manager\BranchServiceTypeManagementController;
use App\Http\Controllers\Manager\CouponManagementController;
use App\Http\Controllers\Manager\MenuManagementController;
use App\Http\Controllers\Manager\OrderManagementController;
use App\Http\Controllers\Manager\PackageDiscountController;
use App\Http\Controllers\Manager\PackageExtraManagementController;
use App\Http\Controllers\Manager\PackageExtraItemManageController ;
use App\Http\Controllers\Manager\ReviewmanagerController;
use App\Http\Controllers\Manager\ServiceTypeManagementController;
use App\Http\Controllers\Owner\BranchController as OwnerBranchController;
use App\Http\Controllers\Owner\BranchStatisticsController;
use App\Http\Controllers\Owner\branchtatisticsontroller;
use App\Http\Controllers\Owner\WorkingDayController;
use App\Http\Controllers\ReportController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/branches/{branch}/packages', [PackageController::class, 'index']);
    Route::get('packages/{id}', [PackageController::class, 'show']);
    Route::get('/packages', [PackageController::class, 'listPackages']);

});
Route::prefix('cart')->middleware('auth:sanctum')->group(function () {

    Route::post('/add', [CartController::class, 'addToCart']);
    Route::post('item/{id}', [CartController::class, 'updateCartItem']);
    Route::get('/packages', [CartController::class, 'getCartPackages']);
    Route::get('items/{cartItemId}', [CartController::class, 'showCartItem']);
    Route::delete('/items/{cartItem}', [CartController::class, 'removeCartItem']);
});

Route::prefix('order')->middleware('auth:sanctum')->group(function () {

    Route::post('/init', [OrderController::class, 'initOrder']);
    Route::post('/create', [OrderController::class, 'createOrder']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::get('/user/orders', [OrderController::class, 'listUserOrders']);
    Route::post('/orders/{id}', [OrderController::class, 'updateOrder']);
    Route::delete('/{id}', [OrderController::class, 'deleteOrder']);

});

// delivery routes
Route::middleware(['auth:sanctum', 'delivery'])->prefix('delivery')->group(function () {

    Route::get('/orders', [DeliveryController::class, 'assignedOrders']);
    Route::get('/assigned-orders/{order}', [DeliveryController::class, 'assignedOrderDetails']);

});


Route::post('login',[AuthController::class,'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verify']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendResetOtp']);

Route::post('/forgot-password/reset', [AuthController::class, 'resetPasswordAfterVerification']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
;

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
     Route::get('orders/Popular-food-week/{branch_id}', [DashboardController::class, 'getPopularPackagesThisWeek']);
     Route::get('orders/best-sell/{branch_id}', [DashboardController::class, 'getBestSellerPackages']);
     Route::get('all_customer/{branch_id}', [DashboardController::class, 'getBranchCustomers']);
     Route::get('/branches/{branch_id}/customers/search', [DashboardController::class, 'searchCustomersByName']);
     Route::get('/branches/{branch_id}/customers/verified', [DashboardController::class, 'getCustomersVerifiedOnDate']);
     Route::get('/branches/{branch_id}/customers/status', [DashboardController::class, 'searchCustomersByStatus']);
     Route::get('/branches/{user_id}/customer', [DashboardController::class, 'getCustomerWithOrders']);
     Route::get('/manager/customers/{user_id}/orders/{status}', [DashboardController::class, 'getCustomerOrdersByStatus']);
     Route::get('package-discounts/management', [PackageDiscountController::class, 'index']);
     Route::post('package-discounts/management', [PackageDiscountController::class, 'store']);
     Route::delete('package-discounts/{id}/management', [PackageDiscountController::class, 'destroy']);
     Route::post('/report', [ReportController::class, 'store']);;
     Route::get('/report', [ReportController::class, 'index']);
     Route::apiResource('delivery-people/manage', DeliveryEmployeeManagementController::class);
     Route::get('/delivery/manage', [DeliveryEmployeeManagementController::class, 'getDeliveryPersons']);
     Route::get('/descount/manage/all', [PackageDiscountController::class, 'getDiscountedPackages']);
     Route::get('/order/delivery/allOrder', [DeliveryEmployeeManagementController::class, 'getBranchDeliveries']);
      Route::get('/manager/delivery-person/{id}/orders', [DeliveryEmployeeManagementController::class, 'getDeliveryPersonOrdersInMyBranch']);
     Route::get('/branches/{branchId}/working-days', [WorkingDayController::class, 'index']);
     /////////////////////
     Route::apiResource('packagesmangement', PackageExtraItemManageController::class);
    ////////////////////////
     Route::get('/branches/categories', [MenuManagementController::class, 'indexForManager']);
     Route::apiResource('branch-service-types', BranchServiceTypeManagementController::class);      Route::post('/coupons/create', [CouponManagementController::class, 'createCoupon']);
     Route::get('/order/manange', [OrderManagementController::class, 'index']);
     Route::get('/order/manange/{id}', [OrderManagementController::class, 'show']);
     Route::post('/order/manange/{id}/approve', [OrderManagementController::class, 'approve']);
     Route::post('/order/manange/{id}/reject', [OrderManagementController::class, 'reject']);
     Route::post('/order/manange/{id}/update-status', [OrderManagementController::class, 'updateStatus']);
     Route::get('/coupons-to-branch', [CouponManagementController::class, 'index']);
     Route::put('/coupons/{id}', [CouponManagementController::class, 'update']);
     Route::delete('/coupons/{id}', [CouponManagementController::class, 'destroy']);
     Route::get('/packages/with/coupons', [CouponManagementController::class, 'packagesWithCoupons']);
     Route::get('/packages_to_category', [MenuManagementController::class, 'getPackagesByCategory']);
    ////////////////////////////////////////////reviews Management
    Route::get('/reviews/manage', [ReviewmanagerController::class, 'getBranchReviewsSummary']);

});
Route::middleware(['auth:sanctum'])->get('service-types', [ServiceTypeManagementController::class, 'index']);
Route::middleware(['auth:sanctum'])->get('/occasion-types', [OccasionTypeController::class, 'index']);
Route::middleware(['auth:sanctum', 'admin_or_owner'])->group(function () {
     Route::apiResource('users', UserManagementController::class);
     Route::apiResource('/branches/management', OwnerBranchController::class);
     Route::post('/branches/{branchId}/working-days', [WorkingDayController::class, 'store']);
     Route::put('/working-days/{id}', [WorkingDayController::class, 'update']);
     Route::delete('/working-days/{id}', [WorkingDayController::class, 'destroy']);
     Route::post('/occasion-types', [OccasionTypeController::class, 'store']);
     Route::put('/occasion-types/{id}', [OccasionTypeController::class, 'update']);
     Route::get('/branches/{branchId}/working-days/owner', [WorkingDayController::class, 'all']);
     Route::delete('/occasion-types/{id}', [OccasionTypeController::class, 'destroy']);
});


Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
     Route::get('/role', [UserManagementController::class, 'allRole']);
     Route::apiResource('restaurants', AdminRestaurantController::class);
     Route::apiResource('complaints', \App\Http\Controllers\Admin\ComplaintController::class);
     Route::post('service-types', [ServiceTypeManagementController::class, 'store']);
     Route::put('service-types/{id}', [ServiceTypeManagementController::class, 'update']);
     Route::delete('service-types/{id}', [ServiceTypeManagementController::class, 'destroy']);
     Route::get('service-types/{id}', [ServiceTypeManagementController::class, 'show']);
});
Route::middleware(['auth:sanctum','owner'])->prefix('owner')->group(function () {
     Route::get('/my-restaurant', [OwnerBranchController::class, 'showRestaurantDetails']);
     Route::post('/branches/{branch}/categories', [OwnerBranchController::class, 'addCategoriesToBranch']);
     Route::get('branch-statistics', [BranchStatisticsController::class, 'getStatistics']);
     Route::get('/OrdersCountbranches', [BranchStatisticsController::class, 'getOrdersCountbranches']);
     Route::get('/RevenueByMonth', [BranchStatisticsController::class, 'getBranchesRevenueByMonth']);
     Route::get('/BranchFoodItemStats', [BranchStatisticsController::class, 'getBranchFoodItemStats']);
     Route::get('/Summary', [BranchStatisticsController::class, 'getOwnerSummary']);
     Route::get('/branch/{id}/statistics', [BranchStatisticsController::class, 'getBranchStatistics']);
});

Route::get('/allbranch', [BranchController::class, 'getAllBranchesWithDetails']);
Route::get('/restaurant', [RestaurantController::class, 'index']);
//Route::get('/restaurants/category/{name}', [RestaurantController::class, 'getByCategory']);
Route::get('/menu/items', [MenuController::class, 'filterFoodItems']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/branches/category/{categoryName}', [BranchController::class, 'getBranchesByCategoryName']);
    Route::get('/branches/{branch}/food-items', [BranchController::class, 'getItems']);
});





