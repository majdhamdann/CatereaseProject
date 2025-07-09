<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\FoodItem;
use App\Models\OrderDetail;
use App\Models\Package;
use Faker\Factory as Faker;
class OrderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $faker = Faker::create();

        $orders = Order::pluck('id');
        //$foodItems = FoodItem::all();
        $packages = Package::all();
        foreach ($orders as $orderId) {

            $selectedPackages = $packages->random($faker->numberBetween(1, 3));

            foreach ($selectedPackages as $package) {
                OrderDetail::create([
                    'order_id'    => $orderId,
                    'package_id'=> $package->id,
                    'quantity'    => $faker->numberBetween(1, 4),
                    'unit_price'  => $package->base_price,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }

    }
}
