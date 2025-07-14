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
}
