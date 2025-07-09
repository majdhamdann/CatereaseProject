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
        DB::table('categories')->insert([
            [
                'name' => 'المشروبات',
                'description' => 'عصائر، مياه، مشروبات غازية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'المقبلات',
                'description' => 'مقبلات باردة وساخنة',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الأطباق الرئيسية',
                'description' => 'أرز، لحوم، دجاج، مأكولات بحرية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الحلويات',
                'description' => 'كعك، فواكه، حلويات شرقية وغربية',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


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
