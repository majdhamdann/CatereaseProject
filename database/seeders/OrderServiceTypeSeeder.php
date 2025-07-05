<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('order_service_types')->insert([
            [
                'order_id' => 1,
                'branch_service_type_id' => 1,
                'quantity' => 5,
                'total_price' => 500.00,
            ],
            [
                'order_id' => 2,
                'branch_service_type_id' => 2,
                'quantity' => 3,
                'total_price' => 225.00,
            ],
        ]);
    }
}
