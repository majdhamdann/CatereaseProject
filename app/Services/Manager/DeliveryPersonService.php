<?php

namespace App\Services\Manager;

use App\Models\Branch;
use App\Models\Delivery;
use App\Models\DeliveryPerson;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryPersonService
{
    public function list()
    {
        return DeliveryPerson::with('user')->get();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'photo' => $data['photo'] ?? null,
                'role_id' => 3,
                'password' => Hash::make($data['password']),
            ]);

            $deliveryPerson = DeliveryPerson::create([
                'user_id' => $user->id,
                'vehicle_type' => $data['vehicle_type'],
                'is_available' => $data['is_available'] ?? true,
            ]);

            return [$user, $deliveryPerson];
        });
    }

    public function update(DeliveryPerson $deliveryPerson, array $data)
    {
        $deliveryPerson->update($data);
        return $deliveryPerson;
    }

    public function delete(DeliveryPerson $deliveryPerson)
    {
        return $deliveryPerson->delete();
    }

    public function getBranchIdForManager()
    {
        return Branch::where('manager_id', Auth::id())->value('id');
    }

    public function getDeliveryPersonOrdersInBranch($deliveryPersonId)
    {
        $branchId = $this->getBranchIdForManager();

        return Delivery::with(['order.orderDetails.package.feedbacks'])
            ->where('delivery_person_id', $deliveryPersonId)
            ->whereHas('order', fn($q) => $q->where('branch_id', $branchId))
            ->get();
    }

    public function getBranchDeliveries()
    {
        $branchId = $this->getBranchIdForManager();

        return Delivery::with(['order', 'deliveryPerson.user'])
            ->whereHas('order', fn($q) => $q->where('branch_id', $branchId))
            ->get();
    }
}
