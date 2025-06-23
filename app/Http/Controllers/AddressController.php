<?php

namespace App\Http\Controllers;

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



}
