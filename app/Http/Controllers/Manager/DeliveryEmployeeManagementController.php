<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPerson;
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

        return response()->json([
            'status' => true,
            'delivery_person' => $deliveryPerson,
        ]);
    }

    public function update(Request $request, $id)
    {
        $deliveryPerson = DeliveryPerson::findOrFail($id);

        $request->validate([
            'vehicle_type' => 'sometimes|string',
            'is_available' => 'sometimes|boolean',
        ]);

        $deliveryPerson->update($request->only('vehicle_type', 'is_available'));

        return response()->json([
            'status' => true,
            'message' => 'Delivery person updated.',
            'delivery_person' => $deliveryPerson,
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
            'name' => $deliveryPerson->user->name ?? null,
            'phone' => $deliveryPerson->user->phone ?? null,
            'email' => $deliveryPerson->user->email ?? null,
            'orders_count' => $deliveryPerson->orders_count ?? 0,
            'vehicle_type' => $deliveryPerson->vehicle_type,
            'status' => $deliveryPerson->is_available ? 'available' : 'unavailable',
        ];
    });
    return response()->json($data);
}

}
