<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class cityseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        City::create([
//            'name'=>'درعا',
//            'country'=>'جاسم'
//
//        ]);
//         City::create([
//            'name'=>'دمشق',
//            'country'=>'مزه'
//
//        ]);
        DB::table('cities')->insert([
            ['id' => 1, 'name' => 'Damascus', 'country' => 'Syria'],
            ['id' => 2, 'name' => 'Aleppo', 'country' => 'Syria'],
            ['id' => 3, 'name' => 'Latakia', 'country' => 'Syria'],
        ]);


    }
}
