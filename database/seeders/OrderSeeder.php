<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Branch;
use Faker\Factory as Faker;
class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $userIds = User::pluck('id');
        $branchIds = Branch::pluck('id');

        foreach (range(1, 20) as $i) {
            Order::create([
                'user_id'       => $faker->randomElement($userIds),
                'branch_id'     => $faker->randomElement($branchIds),
                'delivery_id'   => null, 
                'status'        => $faker->randomElement(['pending', 'confirmed', 'preparing', 'delivered', 'cancelled']),
                'promo_code_id' => null, 
                'total_price'   => $faker->randomFloat(2, 20, 200),
                'address_id'    => null, 
                'cart_id'       => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
    }
}
}