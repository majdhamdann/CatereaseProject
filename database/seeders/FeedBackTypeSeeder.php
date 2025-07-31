<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedBackTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('feedback_types')->delete();

        DB::table('feedback_types')->insert([
            ['target_type' => 'restaurant', 'target_ref_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['target_type' => 'food_item', 'target_ref_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

}
