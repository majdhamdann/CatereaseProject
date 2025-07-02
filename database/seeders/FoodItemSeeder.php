<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class FoodItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $faker = Faker::create();
        $categories = \App\Models\Category::all();

        foreach ($categories as $category) {
            for ($i = 0; $i < 5; $i++) {
                FoodItem::create([
                    'branch_id' =>1,
                    'category_id' => 1,
                    'name' => $faker->words(2, true),
                    'description' => $faker->sentence,
                    'price' => $faker->randomFloat(2, 10, 100),
                    'discount_price' => $faker->randomFloat(2, 5, 90),
                    'photo' => 'food.jpg',
                    'available' => true,
                    'calories' => $faker->numberBetween(100, 900),
                    'type' => $faker->randomElement([ 'veg', 'non_veg']),
                ]);
            }
        }
    }
}
