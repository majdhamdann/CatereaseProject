<?php
namespace App\Services;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Mail\WelcomeEmail;
use Illuminate\Auth\AuthenticationException;

class UserAuthService
{
    public function registerUser(array $data)
{

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role_id' => 3,
        'phone' => $data['phone'],
        'gender' => $data['gender'],
        'verified' => false,
        'device_token' => $data['device_token'] ?? null,
    ]);

    $otp = rand(100000, 999999);

    Otp::where('user_id', $user->id)->delete();

    Otp::create([
       'user_id' => $user->id,
        'otp' => $otp,
        'expires_at' => Carbon::now()->addMinutes(50),
    ]);

    Mail::to($user->email)->send(new WelcomeEmail($user, $otp));

    return $user;
}

     public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

         if (!$user || !Hash::check($credentials['password'], $user->password)) {
           throw new AuthenticationException('Invalid credentials.');
        }

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

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
