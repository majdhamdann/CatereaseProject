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

        DB::table('cities')->insert([
            ['id' => 1, 'name' => 'Damascus', 'country' => 'Syria'],
            ['id' => 2, 'name' => 'Aleppo', 'country' => 'Syria'],
            ['id' => 3, 'name' => 'Latakia', 'country' => 'Syria'],
            ['id' => 4, 'name' => 'Tartous', 'country' => 'Syria'],
            ['id' => 5, 'name' => 'Homs', 'country' => 'Syria'],
            ['id' => 6, 'name' => 'Daraa', 'country' => 'Syria'],
            ['id' => 7, 'name' => 'Quneitra', 'country' => 'Syria'],
            ['id' => 8, 'name' => 'Hama', 'country' => 'Syria'],
            ['id' => 9, 'name' => 'Idlib', 'country' => 'Syria'],
            ['id' => 10, 'name' => 'Al-Hasakah', 'country' => 'Syria'],
            ['id' => 11, 'name' => 'Raqqa', 'country' => 'Syria'],
            ['id' => 12, 'name' => 'Deir ez-Zor', 'country' => 'Syria'],
            ['id' => 13, 'name' => 'Rif Dimashq', 'country' => 'Syria'],
            ['id' => 14, 'name' => 'Al-Suwayda', 'country' => 'Syria'],
        ]);

    }
}
