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
        $foodCategories = [
            ['name' => 'Eastern'],
            ['name' => 'Western'],
            ['name' => 'Desserts'],
            ['name' => 'Beverages'],
        ];

        foreach ($foodCategories as $category) {
            FoodCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
