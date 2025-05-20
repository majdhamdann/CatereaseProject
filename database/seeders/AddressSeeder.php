<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;
use App\Models\City;
use Faker\Factory as Faker;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $users = User::pluck('id');
        $cities = City::pluck('id');

        foreach ($users as $user_id) {
            Address::create([
                'user_id'    => $user_id,
                'city_id'    => $faker->randomElement($cities),
                'street'     => $faker->streetName,
                'building'   => 'B' . $faker->numberBetween(1, 50),
                'floor'      => $faker->numberBetween(1, 10),
                'apartment'  => $faker->numberBetween(1, 100),
                'coordinate' => $faker->latitude . ',' . $faker->longitude,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
