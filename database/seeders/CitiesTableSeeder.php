<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
    ['name' => 'Damascus', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Rural Damascus', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Tartus', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Aleppo', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Hama', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Homs', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Latakia', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Raqqa', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Deir ez-Zor', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Hasakah', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Daraa', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Al-Suwayda', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Idlib', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Quneitra', 'country' => 'Syria', 'created_at' => now(), 'updated_at' => now()],
];

        DB::table('cities')->insert($cities);
    }
}