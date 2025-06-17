<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('users')->insert([
            [
                'Full_Name' => 'Manager User',
                'role_id' => 2,
                'phone' => 123456789,
                'photo' => 'default.jpg',
                'gender' => 'm',
                'email' => 'Manager@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456789'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [
                 'Full_Name' => 'Customer User',
                 'role_id' => 3,
                 'phone' => 599123456,
                 'photo' => 'default.jpg',
                 'gender' => 'f',
                 'email' => 'customer@example.com',
                 'email_verified_at' => now(),
                 'password' => Hash::make('123456789'),
                 'remember_token' => Str::random(10),
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
        ]);
    }
}
