<?php
namespace App\Services;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Mail\WelcomeEmail;

class UserAuthService
{
    public function registerUser(array $data)
    {
        $photoPath = $data['photo']->store('photos', 'public');
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => 3,
            'phone' => $data['phone'],
            'photo' => $photoPath,
            'gender' => $data['gender'],
            'verified' => false,
        ]);
        $otp = rand(100000, 999999);

        Otp::where('user_id', $user->id)->delete();

        Otp::create([
           'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(50),
        ]);

        Mail::to($user->email)->send(new WelcomeEmail($user, $otp));

        $user->photo = asset('storage/' . $user->photo);

        return $user;
    }
     public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
             return response()->json([
                'message' => 'Invalid Credentials'
            ],401);
        }

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

        $user->photo = asset('storage/' . $user->photo);

        return [
            'access_token' => $token,
            'user' => $user,
        ];
    }
    public function verify($userId, $otp)
    {
        $otpRecord = Otp::where('user_id', $userId)
                        ->where('otp', $otp)
                        ->where('expires_at', '>', Carbon::now())
                        ->first();

        if (!$otpRecord) {
            return ['status' => false, 'message' => 'Invalid or expired OTP'];
        }

        $user = User::find($userId);
        if ($user) {
            $user->verified = true;
            $user->save();
        }

        $otpRecord->delete();

        return ['status' => true, 'message' => 'OTP verified successfully'];
    }
}
