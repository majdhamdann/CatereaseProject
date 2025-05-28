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
            'Full_Name' => $data['Full_Name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'],
            'phone' => $data['phone'],
            'photo' => $photoPath,
            'gender' => $data['gender'],
            'verified' => false,
        ]);
        $otp = rand(100000, 999999);

        Otp::where('identifier', $user->email)->delete();

        Otp::create([
            'identifier' => $user->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addSeconds(50),
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
}
