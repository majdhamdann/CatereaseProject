<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service_types')->insert([
            ['name' => 'Open Buffet', 'description' => 'All-you-can-eat buffet', 'pricing_model' => 'per_person'],
            ['name' => 'Set Menu', 'description' => 'Fixed menu with set items', 'pricing_model' => 'fixed'],
            ['name' => 'Table Service', 'description' => 'Served at your table', 'pricing_model' => 'hourly'],
        ]);
    }
}
