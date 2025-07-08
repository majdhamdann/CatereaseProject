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
                'name' => 'Manager User',
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

                 'name' => 'Customer User',
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
             [

                'name' => 'Admin User',
                'role_id' => 1,
                'phone' => 123456789,
                'photo' => 'default.jpg',
                'gender' => 'm',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456789'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
             [

                 'name' => 'Manager Damascus',
                 'role_id' => 2,
                 'phone' => 599123455,
                 'photo' => 'default.jpg',
                 'gender' => 'm',
                 'email' => 'damascus.manager@example.com',
                 'email_verified_at' => now(),
                 'password' =>  Hash::make('12345678'),
                 'remember_token' => Str::random(10),
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [

                 'name' => 'Manager Aleppo',
                 'role_id' => 2,
                 'phone' => 599123457,
                 'photo' => 'default.jpg',
                 'gender' => 'm',
                 'email' => 'aleppo.manager@example.com',
                 'email_verified_at' => now(),
                 'password' => Hash::make('12345678'),
                 'remember_token' => Str::random(10),
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [

                 'name' => 'Manager Latakia',
                 'role_id' => 2,
                 'phone' => 599123459,
                 'photo' => 'default.jpg',
                 'gender' => 'f',
                 'email' => 'latakia.manager@example.com',
                 'email_verified_at' => now(),
                 'password' => Hash::make('12345678'),
                 'remember_token' => Str::random(10),
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [

                 'name' => 'DeliveryStaff Latakia',
                 'role_id' => 5,
                 'phone' => 599123450,
                 'photo' => 'default.jpg',
                 'gender' => 'f',
                 'email' => 'latakia.DeliveryStaff@example.com',
                 'email_verified_at' => now(),
                 'password' => Hash::make('12345678'),
                 'remember_token' => Str::random(10),
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [

                 'name' => 'DeliveryStaff ',
                 'role_id' => 5,
                 'phone' => 599123454,
                 'photo' => 'default.jpg',
                 'gender' => 'f',
                 'email' => 'o.DeliveryStaff@example.com',
                 'email_verified_at' => now(),
                 'password' => Hash::make('12345678'),
                 'remember_token' => Str::random(10),
                 'created_at' => now(),
                 'updated_at' => now(),
             ],


        ]);

    }
}
