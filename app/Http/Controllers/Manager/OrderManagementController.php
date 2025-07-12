<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
     public function index(Request $request)
    {
        $manager = auth()->user();
        $branch = Branch::where('manager_id', $manager->id)->first();

        if (!$branch) {
            return response()->json(['message' => 'Manager has no branch assigned.'], 404);
        }

        $orders = Order::with(['orderDetails.package'])
            ->where('branch_id', $branch->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    public function show($id)
    {
        $manager = auth()->user();
        $branchId = Branch::where('manager_id', $manager->id)->value('id');

        $order = Order::with(['orderDetails.package'])
            ->where('id', $id)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        return response()->json($order);
    }

    public function approve($id)
    {
        $manager = auth()->user();
        $branchId = Branch::where('manager_id', $manager->id)->value('id');

        $order = Order::where('id', $id)->where('branch_id', $branchId)->firstOrFail();

        $order->is_approved = true;
        $order->approved_by = $manager->id;
        $order->approved_at = now();
        $order->save();

        return response()->json(['message' => 'Order approved successfully']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $manager = auth()->user();
        $branchId = Branch::where('manager_id', $manager->id)->value('id');

        $order = Order::where('id', $id)->where('branch_id', $branchId)->firstOrFail();

        $order->is_approved = false;
        $order->rejection_reason = $request->rejection_reason;
        $order->approved_by = $manager->id;
        $order->approved_at = now();
        $order->save();

        return response()->json(['message' => 'Order rejected']);
    }
    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:pending,confirmed,preparing,delivered,cancelled',
    ]);

    $manager = auth()->user();
    $branchId = Branch::where('manager_id', $manager->id)->value('id'); // ✅ هنا التعديل

    $order = Order::where('id', $id)
        ->where('branch_id', $branchId)
        ->firstOrFail();

    $oldStatus = $order->status;
    $order->status = $request->status;
    $order->save();

    return response()->json([
        'message' => 'Order status updated',
        'from' => $oldStatus,
        'to' => $order->status,
    ]);
}

}
