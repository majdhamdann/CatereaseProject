<?php


namespace App\Services;

use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressService
{
    protected $addressRepository;

    public function __construct(AddressRepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only([
                'city_id', 'street', 'building', 'floor', 'apartment', 'latitude', 'longitude'
            ]);
            $data['user_id'] = Auth::id();

            $address = $this->addressRepository->create($data);

            DB::commit();

            return [
                'status' => 'success',
                'code' => 201,
                'message' => 'Address created successfully',
                'data' => $address
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to create address',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getAllForCurrentUser()
    {
        try {
            $userId = Auth::id();

            $addresses = $this->addressRepository->getByUserId($userId);

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Addresses retrieved successfully',
                'data' => $addresses->map(function ($address) {
                    return [
                        'id'         => $address->id,
                        'user_id'    => $address->user_id,
                        'city'       => optional($address->city)->name,
                        'city_id'    => $address->city_id,
                        'street'     => $address->street,
                        'building'   => $address->building,
                        'floor'      => $address->floor,
                        'apartment'  => $address->apartment,
                        'latitude'   => $address->latitude,
                        'longitude'  => $address->longitude,
                        'is_default' => $address->is_default,
                    ];
                }),
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to retrieve addresses',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function update($id, $request)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $address = $this->addressRepository->findByIdAndUser($id, $userId);

            if (!$address) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Address not found or unauthorized',
                ];
            }

            $data = $request->only([
                'city_id', 'street', 'building', 'floor', 'apartment', 'latitude', 'longitude'
            ]);

            $updated = $this->addressRepository->update($id, $data);

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Address updated successfully',
                'data' => $updated,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to update address',
                'error' => $e->getMessage(),
            ];
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $address = $this->addressRepository->findByIdAndUser($id, $userId);

            if (!$address) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Address not found or unauthorized',
                ];
            }

            $this->addressRepository->delete($id);

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Address deleted successfully',
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to delete address',
                'error' => $e->getMessage(),
            ];
        }
    }
    public function setDefault($id)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $address = $this->addressRepository->findByIdAndUser($id, $userId);

            if (!$address) {
                DB::rollBack();
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Address not found or unauthorized'
                ];
            }


            $this->addressRepository->unsetDefaultForUser($userId);
            $address->update(['is_default' => true]);

            DB::commit();

            return [
                'status' => 'success',
                'code' => 200,
                'message' => 'Default address set successfully',
                'data' => $address
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => 'error',
                'code' => 500,
                'message' => 'Failed to set default address',
                'error' => $e->getMessage()
            ];
        }
    }





}


