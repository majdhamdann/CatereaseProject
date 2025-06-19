<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\CustomerService;
use App\Http\Requests\UpdateCustomerProfileRequest;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function show()
    {
        $result = $this->customerService->getProfile();

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,

        ], $result['code']);
    }

    public function update(UpdateCustomerProfileRequest $request)
    {
        $result = $this->customerService->updateProfile($request);

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
           // 'request_data' => $request->all()
        ], $result['code']);
    }
}
