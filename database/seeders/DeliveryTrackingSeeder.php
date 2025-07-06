<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryTracking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DeliveryTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $deliveries = Delivery::all();

        foreach ($deliveries as $delivery) {
            DeliveryTracking::create([
                'delivery_id' => $delivery->id,
                'delivery_person_id' => $delivery->delivery_person_id,
                'latitude' => $faker->latitude(33, 36),
                'longitude' => $faker->longitude(35, 39),
                'recorded_at' => now()
            ]);
        }
    }
}
