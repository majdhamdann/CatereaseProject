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
       

  
            Category::create([
                'branch_id' => 1,
                'name' => 'Fast Food',
                'description' => 'Delicious fast food'
            ]);

            Category::create([
                'branch_id' => 1,
                'name' => 'Oriental',
                'description' => 'Middle eastern cuisine'
            ]);
       
    }
}
