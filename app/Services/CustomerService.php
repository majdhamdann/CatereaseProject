<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
                    'photo' => $user->photo,
                    'role' => $user->role,
                    'addresses' => $user->addresses->map(function ($address) {
                        return [
                            'id' => $address->id,
                            'city' => optional($address->city)->name,
                            'country' => optional($address->city)->country,
                            'street' => $address->street,
                            'building' => $address->building,
                            'floor' => $address->floor,
                            'apartment' => $address->apartment,
                            'latitude'   => $address->latitude,
                            'longitude'  => $address->longitude,
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

    public function updatePassword($request)
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


            if (!Hash::check($request->input('current_password'), $user->password)) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'code' => 403,
                    'message' => 'Current password is incorrect.',
                ];
            }


            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Password updated successfully.',
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Something went wrong while updating password.',
                'error' => $e->getMessage(),
            ];
        }
    }
}

