<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function getProfile()
    {
        try {
            DB::beginTransaction();

            $user = User::with(['role', 'addresses.city'])->find(Auth::id());

            if (!$user) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Unauthorized. Please log in.',
                ];
            }

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'User profile retrieved successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'photo_url' => $user->photo,
                    'role' => optional($user->role)->name,
                    'addresses' => $user->addresses->map(function ($address) {
                        return [
                            'address_id' => $address->address_id,
                            'city' => optional($address->city)->name,
                            'country' => optional($address->city)->country,
                            'street' => $address->street,
                            'building' => $address->building,
                            'floor' => $address->floor,
                            'apartment' => $address->apartment,
                            'coordinate' => $address->coordinate,
                        ];
                    }),
                    'created_at' => $user->created_at->format('Y-m-d'),
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Something went wrong while retrieving profile',
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateProfile($request)
    {
        try {
            DB::beginTransaction();

            $user = User::find(Auth::id());

            if (!$user) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'code' => 401,
                    'message' => 'Unauthorized. Please log in.',
                ];
            }

            $data = $request->only(['name', 'email', 'phone', 'gender', 'photo']);

            $user->update($data);

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Profile updated successfully',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'photo' => $user->photo,
                    'updated_at' => $user->updated_at->format('Y-m-d H:i'),
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Something went wrong while updating profile',
                'error' => $e->getMessage()
            ];
        }
    }
}

