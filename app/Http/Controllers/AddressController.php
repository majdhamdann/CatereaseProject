<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AddressService;

class AddressController extends Controller
{

    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function store(StoreAddressRequest $request)
    {
        $result = $this->addressService->store($request);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ], $result['code']);
    }

    public function index()
    {
        $result = $this->addressService->getAllForCurrentUser();

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ], $result['code']);
    }

    public function update($id, UpdateAddressRequest $request)
    {
        $result = $this->addressService->update($id, $request);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ], $result['code']);
    }

    public function delete($id)
    {
        $result = $this->addressService->delete($id);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'error' => $result['error'] ?? null,
        ], $result['code']);
    }

    public function setDefault($id)
    {
        $result = $this->addressService->setDefault($id);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ], $result['code']);
    }
    public function getCities()
    {
        $cities = City::select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'data'   => $cities,
        ]);
    }

    public function getDistrictsByCity($cityId)
    {
        $districts = District::where('city_id', $cityId)
            ->select('id', 'name')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $districts,
        ]);
    }

    public function getAreasByDistrict($districtId)
    {
        $areas = Area::where('district_id', $districtId)
            ->select('id', 'name')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $areas,
        ]);
    }

    public function getAllHierarchy()
    {
        $cities = City::with([
            'districts' => function ($q) {
                $q->select('id', 'name', 'city_id')
                    ->with(['areas' => function ($qq) {
                        $qq->select('id', 'name', 'district_id');
                    }]);
            }
        ])->select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'data'   => $cities,
        ]);
    }








}
