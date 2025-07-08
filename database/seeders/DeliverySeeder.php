<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryPerson;
use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $orders = Order::inRandomOrder()->take(5)->get();
        $deliveryPeople = DeliveryPerson::pluck('id');

        foreach ($orders as $order) {
            Delivery::create([
                'order_id' => $order->id,
                'delivery_person_id' => $faker->randomElement($deliveryPeople),
                'status' => $faker->randomElement(['pending', 'assigned', 'in_progress', 'delivered', 'cancelled']),
                'estimated_time' => now()->addMinutes(rand(20, 60)),
                'delivered_at' => null,
                'notes' => $faker->sentence()
            ]);
        }
    }
}
