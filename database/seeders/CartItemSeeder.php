<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Package;
use App\Models\Branch;
use Faker\Factory as Faker;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $users = User::pluck('id');
        $packages = Package::pluck('id');
        $branches = Branch::pluck('id');

        foreach (range(1, 20) as $i) {
            CartItem::create([
                'user_id' => $users->random(),
                'package_id' => $packages->random(),
                'branch_id' => $branches->random(),
                'quantity' => $faker->numberBetween(1, 5),
                'notes' => $faker->optional()->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
