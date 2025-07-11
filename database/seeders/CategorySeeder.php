<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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



    }
}
