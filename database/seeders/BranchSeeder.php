<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\City;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $faker = Faker::create();
//        $cities = City::pluck('id');
//        Branch::create([
//
//            'restaurant_id' => '1',
//            'city_id'    => $faker->randomElement($cities),
//
//            'location_note' => 'The most famous restaurants and the most delicious foods ',
//            'latitude' => $faker->latitude,
//            'longitude' => $faker->longitude,
//
//            'description' =>' this branch is ...........',
//            'photo' =>'logBranch.png',
//            'manager_id'=>1,
//            'created_at'=>now(),
//            'updated_at'=>now()
//
//        ]);
        DB::table('branches')->insert([
            [

                'restaurant_id' => 1,
                'manager_id' => 4,
                'city_id' => 1,
                'description' => 'Main branch in Damascus',
                'photo' => 'https://example.com/images/damascus_branch.png',
                'location_note' => 'Near Umayyad Square',
                'latitude' => 33.5138,
                'longitude' => 36.2765,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'restaurant_id' => 2,
                'manager_id' => 5,
                'city_id' => 2,
                'description' => 'Aleppo downtown branch',
                'photo' => 'https://example.com/images/aleppo_branch.png',
                'location_note' => 'Close to Aleppo Citadel',
                'latitude' => 36.2021,
                'longitude' => 37.1343,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [

                'restaurant_id' => 1,
                'manager_id' => 6,
                'city_id' => 3,
                'description' => 'Latakia branch',
                'photo' => 'https://example.com/images/latakia_branch.png',
                'location_note' => 'On the Corniche Road',
                'latitude' => 35.5196,
                'longitude' => 35.7794,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);



    }
}
