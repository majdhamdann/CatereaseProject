<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{

    public function getBillByOrderId($orderId)
    {
        $order = Order::with(['bill', 'orderDetails.package'])->find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (!$order->bill) {
            return response()->json([
                'success' => false,
                'message' => 'No bill has been generated for this order yet.',
            ], 404);
        }


        $prepaymentRequired   = false;
        $prepaymentPercentage = null;
        $prepaymentAmount     = null;

        foreach ($order->orderDetails as $detail) {
            $package = $detail->package;
            if ($package && $package->prepayment_required) {
                $prepaymentRequired   = true;
                $prepaymentPercentage = $package->prepayment_amount . '%';
                $prepaymentAmount     = round($order->total_price * ($package->prepayment_amount / 100), 2);
                break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Bill retrieved successfully.',
            'data' => [
                'bill_id'              => $order->bill->id,
                'order_id'             => $order->id,
                'prepayment_percentage'=> $prepaymentPercentage,
                'prepayment_amount'    => $prepaymentAmount,
                'total_amount'         => $order->bill->amount,
                'status'               => $order->bill->status,
                'issued_at'            => $order->bill->issued_at,

            ]
        ], 200);
    }




}
