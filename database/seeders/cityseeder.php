<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class cityseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create([
            'name'=>'درعا',
            'country'=>'جاسم'

        ]);
         City::create([
            'name'=>'دمشق',
            'country'=>'مزه'

        ]);
    }
}
