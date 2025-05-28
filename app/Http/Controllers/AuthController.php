<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
class AuthController extends Controller
{
    public function login(Request $request){
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


   

public function register(Request $request)
{
    $request->validate([
        'Full_Name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|confirmed',
        'role_id' => 'required|exists:roles,id',
        'phone' => 'required|numeric',
        'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        'gender' => 'required|in:f,m',
    ]);
    $photoPath = $request->file('photo')->store('photos', 'public');
    $user = User::create([
        'Full_Name' => $request->Full_Name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $request->role_id,
        'phone' => $request->phone,
        'photo' => $photoPath,
        'gender' => $request->gender,
        'verified' => false,
    ]);
   
    $otp = rand(100000, 999999);

    Otp::where('identifier', $user->email)->delete();

    // حفظ OTP جديد
    Otp::create([
        'identifier' => $user->email,
        'otp' => $otp,
        'expires_at' => Carbon::now()->addSeconds(50),
    ]);

    Mail::to($user->email)->send(new WelcomeEmail($user, $otp));
    $url = asset('storage/' . $user->photo);
    $user->photo = $url;
    return response()->json([
        'message' => 'User registered. OTP sent to email.',
        'user'=>$user 
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
