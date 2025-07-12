<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Package;
use App\Models\FoodItem;
use App\Models\BranchServiceType;
use App\Models\PackageExtra;
use Faker\Factory as Faker;


class PackageExtrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        $packages = Package::all();
        $foodItems = FoodItem::pluck('id')->toArray();
        $branchServiceTypes = BranchServiceType::pluck('id')->toArray();

        foreach ($packages as $package) {
            //  food_item
            if (!empty($foodItems)) {
                $foodItemId = $faker->randomElement($foodItems);
                $foodItem = FoodItem::find($foodItemId);

                PackageExtra::create([
                    'package_id' => $package->id,
                    'type' => 'food_item',
                    'food_item_id' => $foodItemId,
                    'branch_service_type_id' => null,
                    'name' => optional($foodItem)->name ?? 'Unknown Food Item',
                    'price' => $faker->randomFloat(2, 20, 100),
                    'is_optional' => true,
                ]);
            }

            //  service
            if (!empty($branchServiceTypes)) {
                $branchServiceTypeId = $faker->randomElement($branchServiceTypes);
                $branchServiceType = \App\Models\BranchServiceType::with('serviceType')->find($branchServiceTypeId);

                PackageExtra::create([
                    'package_id' => $package->id,
                    'type' => 'service',
                    'food_item_id' => null,
                    'branch_service_type_id' => $branchServiceTypeId,
                    'name' => optional($branchServiceType->serviceType)->name ?? 'Unnamed Service',
                    'price' => $faker->randomFloat(2, 50, 150),
                    'is_optional' => true,
                ]);
            }


            //  simple
            PackageExtra::create([
                'package_id' => $package->id,
                'type' => 'simple',
                'food_item_id' => null,
                'branch_service_type_id' => null,
                'name' => 'Extra hour',
                'price' => 80,
                'is_optional' => true,
            ]);
        }

    }
}
