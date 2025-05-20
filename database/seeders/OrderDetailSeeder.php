<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\FoodItem;
use App\Models\OrderDetail;
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
        $foodItems = FoodItem::all();

        foreach ($orders as $orderId) {
            // لكل طلب، نضيف 1 إلى 3 أصناف طعام عشوائية
            $items = $foodItems->random($faker->numberBetween(1, 3));

            foreach ($items as $item) {
                OrderDetail::create([
                    'order_id'    => $orderId,
                    'food_item_id'=> $item->id,
                    'quantity'    => $faker->numberBetween(1, 4),
                    'unit_price'  => $item->price,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    
    }
}
