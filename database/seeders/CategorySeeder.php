<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['id' => 1, 'name' => 'Eastern']);
        Category::create(['id' => 2, 'name' => 'Western']);
        Category::create(['id' => 3, 'name' => 'Desserts']);
        Category::create(['id' => 4, 'name' => 'Soft Drinks']);


//            Category::create([
//                'branch_id' => 1,
//                //'name' => 'Fast Food',
//                'food_category_id'=>1,
//                //'description' => 'Delicious fast food'
//            ]);
//
//            Category::create([
//                'branch_id' => 2,
//                'food_category_id'=>1,
//                //'name' => 'Oriental',
//                //'description' => 'Middle eastern cuisine'
//            ]);
//        Category::create([
//            'branch_id' => 3,
//            'food_category_id'=>1,
//            //'name' => 'Oriental',
//            //'description' => 'Middle eastern cuisine'
//        ]);
//        Category::create([
//            'branch_id' => 4,
//            'food_category_id'=>1,
//            //'name' => 'Oriental',
//            //'description' => 'Middle eastern cuisine'
//        ]);

    }
}
