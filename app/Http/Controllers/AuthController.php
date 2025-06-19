<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Mail\WelcomeEmail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\SendOtpNotification;
use App\Services\UserAuthService;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserAuthService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterUserRequest $request)
    {
        $user = $this->userService->registerUser($request->all());

        return response()->json([
            'message' => 'User registered. OTP sent to email.',
            'user' => $user
        ]);
    }
    public function login(LoginRequest $request)
    {
        $data = $this->userService->login($request->validated());

        return response()->json($data);
    }
    public function verify(VerifyOtpRequest $request)
    {
        $result = $this->userService->verify($request->user_id, $request->otp);

        if (!$result['status']) {
            return response()->json(['message' => $result['message']], 400);
        }

        return response()->json(['message' => $result['message']]);
    }


}
