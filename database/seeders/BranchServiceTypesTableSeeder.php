<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchServiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('branch_service_types')->truncate();

        $data = [

            [
                'branch_id' => 1,
                'service_type_id' => 1,
                'custom_price' => 15.00,
                'service_cost' => 5.00,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'branch_id' => 1,
                'service_type_id' => 2,
                'custom_price' => null,
                'service_cost' => 0.00,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],


            [
                'branch_id' => 2,
                'service_type_id' => 1,
                'custom_price' => 20.00,
                'service_cost' => 7.00,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'branch_id' => 2,
                'service_type_id' => 3,
                'custom_price' => null,
                'service_cost' => 0.00,
                'is_available' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],


            [
                'branch_id' => 3,
                'service_type_id' => 2,
                'custom_price' => 10.00,
                'service_cost' => 2.00,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('branch_service_types')->insert($data);

        $this->command->info('Branch Service Types seeded successfully!');
    }
}
