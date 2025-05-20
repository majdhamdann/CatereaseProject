<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class resturantseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Restaurant::create([

            'name' => 'مطعم الاول',

            'description' => 'اشهر المطاعم والذ الاطعمة ',

            'logo_url' =>'log.png',
            'owner_id'=>1,
            'created_at'=>now(),
            'updated_at'=>now()

        ]);
    }
}
