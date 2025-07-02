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

        FoodCategory::create(['id' => 1, 'name' => 'Eastern']);
        FoodCategory::create(['id' => 2, 'name' => 'Western']);
        FoodCategory::create(['id' => 3, 'name' => 'Desserts']);
        FoodCategory::create(['id' => 4, 'name' => 'Soft Drinks']);
    }
}
