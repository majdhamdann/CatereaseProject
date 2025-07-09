<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PackageItem;
use App\Models\Package;
use App\Models\FoodItem;

class PackageItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = Package::all();
        $foodItems = FoodItem::all();

        foreach ($packages as $package) {

            $selectedItems = $foodItems->random(rand(3, 6));

            foreach ($selectedItems as $item) {
                PackageItem::create([
                    'package_id'   => $package->id,
                    'food_item_id' => $item->id,
                    'quantity'     => rand(1, 3),
                    'is_optional'  => rand(0, 1)
                ]);
            }
        }

    }
}
