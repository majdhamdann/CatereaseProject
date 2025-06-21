<?php
namespace App\Repositories;

use App\Models\Address;
use App\Repositories\Contracts\AddressRepositoryInterface;

class AddressRepository implements AddressRepositoryInterface
{
    public function create(array $data)
    {
        return Address::create($data);
    }

    public function getByUserId($userId)
    {
        return Address::with('city')->where('user_id', $userId)->get();
    }

    public function delete($id)
    {
        return Address::destroy($id);
    }
    public function update($id, array $data)
    {
        $address = Address::findOrFail($id);
        $address->update($data);
        return $address;
    }

    public function findByIdAndUser($id, $userId)
    {
        return Address::where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }



}
