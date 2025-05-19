<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    //حساب ارباح مطعم معين
    public function calculateRestaurantProfit($restaurant_id)
   {
     $branchIds = DB::table('branches')
        ->where('Restaurant_id', $restaurant_id)
        ->pluck('id');

     $income = DB::table('orders')
        ->whereIn('branch_id', $branchIds)
        ->where('status', 'delivered')
        ->sum('total_price');

      $expenses = DB::table('expenses')
        ->where('restaurant_id', $restaurant_id)
        ->sum('amount');

      $netProfit = $income - $expenses;

      return response()->json([
        'restaurant_id' => $restaurant_id,
        'total_income' => $income,
        'total_expenses' => $expenses,
        'net_profit' => $netProfit,
     ]);
    }
}
