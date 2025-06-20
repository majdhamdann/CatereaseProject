<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\CustomerAddressService;

class CustomerAddressController extends Controller
{

    protected $addressService;

    public function __construct(CustomerAddressService $addressService)
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
}
