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
  public function sendResetOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $email = $request->input('email');

    $user = User::where('email', $email)->first();

    $otp = rand(100000, 999999);

    Otp::where('user_id', $user->id)->delete();

    Otp::create([
        'user_id' => $user->id,
        'otp' => $otp,
        'expires_at' => now()->addMinutes(30),
    ]);

    Mail::to($user->email)->send(new WelcomeEmail($user, $otp));

    return response()->json(['status' => true, 'message' => 'OTP sent to your email', 'user_id' => $user->id,]);
}

public function resetPasswordAfterVerification(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'newPassword' => 'required|string|min:8|confirmed',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['status' => false, 'message' => 'User not found'], 404);
    }

    $user->password = Hash::make($request->newPassword);
    $user->save();

    return response()->json(['status' => true, 'message' => 'Password reset successfully']);
}
public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    $user = $request->user();
    $user->status = 'deleted';
    $user->save();

    return response()->json(['message' => 'Logout  successfully']);
}





}
