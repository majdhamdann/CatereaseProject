<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodCategory;

class FoodCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        FoodCategory::create([
            'branch_id' => 1,
            //'name' => 'Fast Food',
            'category_id'=>1,
            //'description' => 'Delicious fast food'
        ]);

        FoodCategory::create([
            'branch_id' => 2,
            'category_id'=>1,
            //'name' => 'Oriental',
            //'description' => 'Middle eastern cuisine'
        ]);
        FoodCategory::create([
            'branch_id' => 3,
            'category_id'=>1,
            //'name' => 'Oriental',
            //'description' => 'Middle eastern cuisine'
        ]);
        FoodCategory::create([
            'branch_id' => 4,
            'category_id'=>1,
            //'name' => 'Oriental',
            //'description' => 'Middle eastern cuisine'
        ]);

    }
}
