<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Delivery;
use App\Models\DeliveryPerson;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DeliveryEmployeeManagementController extends Controller
{
        public function index()
    {
        $deliveryPeople = DeliveryPerson::with('user')->get();
        return response()->json([
            'status' => true,
            'delivery_people' => $deliveryPeople,
        ]);
    }

    public function store(Request $request)
  {
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|numeric',
        'gender' => 'required|in:m,f',
        'photo' => 'nullable|string',
        'password' => 'required|string|min:6',
        'vehicle_type' => 'required|string',
        'is_available' => 'boolean',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'gender' => $request->gender,
        'photo' => $request->photo,
        'role_id' => 3, 
        'password' => bcrypt($request->password),
    ]);

    $deliveryPerson = DeliveryPerson::create([
        'user_id' => $user->id,
        'vehicle_type' => $request->vehicle_type,
        'is_available' => $request->is_available ?? true,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Delivery person and user created successfully.',
        'user' => $user,
        'delivery_person' => $deliveryPerson,
    ], 201);
   }


public function show($id)
{
    $deliveryPerson = DeliveryPerson::with('user')->findOrFail($id);

    $reviewsCount = $deliveryPerson->feedbacks()->count();

    $deliveredOrdersCount = $deliveryPerson->deliveries()
        ->whereHas('order', fn($q) => $q->where('status', 'delivered'))->count();

    $cancelledOrdersCount = $deliveryPerson->deliveries()
        ->whereHas('order', fn($q) => $q->where('status', 'cancelled'))->count();

    $todayEarnings = $deliveryPerson->deliveries()
        ->whereDate('created_at', now()->toDateString())
        ->whereHas('order', fn($q) => $q->where('status', 'delivered'))
        ->with('order')
        ->get()
        ->sum(fn($delivery) => $delivery->order->total_price ?? 0);

    $managerId = auth()->id();
    $branchId = Branch::where('manager_id', $managerId)->value('id');

    if (!$branchId) {
        return response()->json([
            'status' => false,
            'message' => 'لا يوجد فرع مرتبط بهذا المدير.',
        ], 403);
    }

    $branchDeliveries = $deliveryPerson->deliveries()
        ->whereHas('order', fn($q) => $q->where('branch_id', $branchId))
        ->with('order')
        ->get()
        ->map(function ($delivery) {
            $status = match ($delivery->order->status) {
                'delivered' => 'paid',
                'cancelled' => 'cancelled',
                default => $delivery->order->status ?? 'unknown',
            };

            return [
                'delivery_id' => $delivery->id,
                'order_id' => $delivery->order->id ?? null,
                'status' => $status,
                'notes' => $delivery->notes,
                'estimated_time' => $delivery->estimated_time,
                'total_price' => $delivery->order->total_price ?? 0,
                'delivered_at' => $delivery->delivered_at,
            ];
        });

    return response()->json([
        'status' => true,
        'delivery_person' => [
            'id' => $deliveryPerson->id,
            'name' => $deliveryPerson->user->name ?? null,
             'gender' => $deliveryPerson->user->gender ?? null,
            'phone' => $deliveryPerson->user->phone ?? null,
            'email' => $deliveryPerson->user->email ?? null,
            'vehicle_type' => $deliveryPerson->vehicle_type,
            'is_available' => $deliveryPerson->is_available,
            'created_at' => $deliveryPerson->created_at,
            'reviews_count' => $reviewsCount,
            'delivered_orders_count' => $deliveredOrdersCount,
            'cancelled_orders_count' => $cancelledOrdersCount,
            'today_earnings' => $todayEarnings,
        ],
        'branch_deliveries' => $branchDeliveries,
    ]);
}



    public function update(Request $request, $id)
{
    $deliveryPerson = DeliveryPerson::findOrFail($id);

    $request->validate([
        'vehicle_type' => 'sometimes|string',
        'is_available' => 'sometimes|boolean',
        'name' => 'sometimes|string',
        'email' => 'sometimes|email|unique:users,email,' . $deliveryPerson->user_id,
        'phone' => 'sometimes|numeric',
        'gender' => 'sometimes|in:m,f',
        'password' => 'sometimes|string|min:6',
    ]);

    $user = $deliveryPerson->user;

    if ($request->has('name')) {
        $user->name = $request->input('name');
    }
    if ($request->has('email')) {
        $user->email = $request->input('email');
    }
    if ($request->has('phone')) {
        $user->phone = $request->input('phone');
    }
    if ($request->has('gender')) {
        $user->gender = $request->input('gender');
    }
    if ($request->has('password')) {
        $user->password = bcrypt($request->input('password'));
    }
    $user->save();

    $deliveryPerson->update($request->only(['vehicle_type', 'is_available']));

    return response()->json([
        'status' => true,
        'message' => 'Delivery person updated.',
        'delivery_person' => $deliveryPerson->load('user'),
    ]);
}


    public function destroy($id)
    {
        $deliveryPerson = DeliveryPerson::findOrFail($id);
        $deliveryPerson->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delivery person deleted.',
        ]);
    }
    public function getDeliveryPersons(Request $request)
{
    $query = DeliveryPerson::with('user')
        ->withCount(['deliveries as orders_count' => function ($q) {
            $q->whereHas('order', function ($q2) {
                $q2->where('status', 'delivered'); 
            });
        }]);

    if ($request->has('name')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        });
    }

    if ($request->has('status')) {
        $status = $request->status === 'available' ? 1 : 0;
        $query->where('is_available', $status);
    }

    if ($request->has('date')) {
        $query->whereDate('created_at', $request->date);
    }

    $deliveryPeople = $query->get();

    $data = $deliveryPeople->map(function ($deliveryPerson) {
        return [
            'id' => $deliveryPerson->id,
            'gender'=>$deliveryPerson->user->gender,
            'status'=>$deliveryPerson->user->status,
            'name' => $deliveryPerson->user->name ?? null,
            'phone' => $deliveryPerson->user->phone ?? null,
            'email' => $deliveryPerson->user->email ?? null,
            'orders_count' => $deliveryPerson->orders_count ?? 0,
            'vehicle_type' => $deliveryPerson->vehicle_type,
            'is_available' => $deliveryPerson->is_available ? 'available' : 'unavailable',
            'created_at'    => $deliveryPerson->created_at->toDateTimeString()
        ];
    });
    return response()->json($data);
}
 public function getBranchDeliveries()
{
    $manager = auth()->user();
    $branch = Branch::where('manager_id', $manager->id)->first();

    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 403);
    }

    $deliveries = Delivery::with(['order', 'deliveryPerson.user'])
        ->whereHas('order', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id);
        })
        ->get();

    $data = $deliveries->map(function ($delivery) {
        return [
            'delivery_id' => $delivery->id,
            'delivery_person_name' => optional($delivery->deliveryPerson->user)->name,
            'order_id' => $delivery->order->id ?? null,
            'order_status' => $delivery->order->status ?? null,
            'order_total' => $delivery->order->total_price ?? null,
            'delivered_at' => $delivery->created_at->toDateTimeString(),
        ];
    });

    return response()->json([
        'status' => true,
        'branch_id' => $branch->id,
        'deliveries' => $data,
    ]);
}


public function getDeliveryPersonOrdersInMyBranch($deliveryPersonId)
{
    $manager = auth()->user();
    $branch = Branch::where('manager_id', $manager->id)->first();
    if (!$branch) {
        return response()->json(['message' => 'لا يوجد فرع مرتبط بك كمدير.'], 403);
    }
    $deliveries = Delivery::with(['order.orderDetails.package.feedbacks'])
        ->where('delivery_person_id', $deliveryPersonId)
        ->whereHas('order', function ($q) use ($branch) {
            $q->where('branch_id', $branch->id);
        })
        ->get();

    if ($deliveries->isEmpty()) {
        return response()->json(['message' => 'لا يوجد طلبات لهذا الموظف في فرعك.'], 404);
    }
    $data = $deliveries->map(function ($delivery) {
        $packages = $delivery->order->orderDetails->map(function ($detail) {
            $package = $detail->package;
            return [
                'package_id' => $package->id ?? null,
                'package_name' => $package->name ?? null,
                'package_image' => $package->image_url ?? null, 
                'package_rating' => round($package->feedbacks->avg('value'), 1) ?? null, 
            ];
        });

        return [
            'delivery_id' => $delivery->id,
            'order_id' => $delivery->order->id ?? null,
            'order_status' => $delivery->order->status ?? null,
            'total_price' => $delivery->order->total_price ?? null,
            'delivered_at' => $delivery->delivered_at ?? $delivery->created_at->toDateTimeString(),
            'packages' => $packages,
        ];
    });

    return response()->json([
        'status' => true,
        'delivery_person_id' => $deliveryPersonId,
        'branch_id' => $branch->id,
        'orders_count' => $data->count(),
        'orders' => $data,
    ]);
}

}
