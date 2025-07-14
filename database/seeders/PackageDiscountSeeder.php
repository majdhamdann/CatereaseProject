<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageDiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\PackageDiscount::create([
            'package_id' => 1,
            'value' => 25,
            'description' => 'Launch Offer 25%',
            'start_at' => now()->subDay(),
            'end_at' => now()->addDays(3),
            'is_active' => true,
        ]);
    }
}
