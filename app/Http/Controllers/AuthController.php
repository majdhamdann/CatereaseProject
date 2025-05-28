<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
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
    public function login1(Request $request){
        $loginUserData = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);
        $user = User::where('email',$loginUserData['email'])->first();
        if(!$user || !Hash::check($loginUserData['password'],$user->password)){
            return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }
        $token = $user->createToken($user->name.'-AuthToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
        ]);
    }


   

    public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string',
    ]);

    $otpRecord = Otp::where('identifier', $request->email)
                    ->where('otp', $request->otp)
                    ->where('expires_at', '>', now())
                    ->first();

    if (!$otpRecord) {
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }

    $user = User::where('email', $request->email)->first();
    if ($user) {
        $user->verified = true;
        $user->save();
    }

    $otpRecord->delete();

    return response()->json(['message' => 'OTP verified successfully']);
}


}
