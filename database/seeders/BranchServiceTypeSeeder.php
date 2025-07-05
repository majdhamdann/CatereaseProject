<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('branch_service_types')->insert([
            [
                'branch_id' => 1,
                'service_type_id' => 1,
                'custom_price' => 100.00,
                'service_cost' => 20.00,
                'is_available' => true,
            ],
            [
                'branch_id' => 1,
                'service_type_id' => 2,
                'custom_price' => 75.00,
                'service_cost' => 10.00,
                'is_available' => true,
            ],
            [
                'branch_id' => 2,
                'service_type_id' => 1,
                'custom_price' => 110.00,
                'service_cost' => 25.00,
                'is_available' => true,
            ],
        ]);
    }
}
