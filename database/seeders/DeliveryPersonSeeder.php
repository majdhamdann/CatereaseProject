<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {

        DB::table('delivery_people')->insert([
            [
                'user_id' => 7,
                'vehicle_type' => 'car',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 8,
                'vehicle_type' => 'motorcycle',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


        DB::table('delivery_branch')->insert([
            [
                'delivery_person_id' => 1,
                'branch_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'delivery_person_id' => 2,
                'branch_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'delivery_person_id' => 2,
                'branch_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
