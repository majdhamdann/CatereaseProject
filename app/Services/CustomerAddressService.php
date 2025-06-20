<?php


namespace App\Services;

namespace App\Services;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAddressService
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
                'city_id', 'street', 'building', 'floor', 'apartment', 'coordinate'
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
}


