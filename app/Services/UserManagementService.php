<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    public function getAllUsers()
    {
        return User::with('role')->get();
    }

    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function getUserById(int $id): array
{
    $user = User::with('role')->findOrFail($id);
    
    $data = ['user' => $user];
    
    if ($user->hasRole('Manager')) {
        $branch = Branch::with(['restaurant', 'city'])
            ->where('manager_id', $user->id)
            ->first();
            
        if ($branch) {
            $data['branch'] = $branch;
        }
    }
    
    if ($user->hasRole('Owner')) {
        $restaurant = Restaurant::with(['branches.manager', 'branches.city'])
            ->where('owner_id', $user->id)
            ->first();
            
        if ($restaurant) {
            $data['restaurant'] = $restaurant;
        }
    }
    
    return $data;
}

    public function updateUser(array $data, int $id): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function deleteUser(int $id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
    public function getManagerBranch($managerId)
{
    return Branch::with(['restaurant', 'city'])
        ->where('manager_id', $managerId)
        ->firstOrFail();
}

public function getOwnerRestaurantWithBranches($ownerId)
{
    return Restaurant::with(['branches' => function($query) {
            $query->with(['manager', 'city']);
        }])
        ->where('owner_id', $ownerId)
        ->firstOrFail();
}

}
