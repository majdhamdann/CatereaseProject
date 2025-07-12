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
                PackageExtra::create([
                    'package_id' => $package->id,
                    'type' => 'food_item',
                    'food_item_id' => $faker->randomElement($foodItems),
                    'branch_service_type_id' => null,
                    'name' => '', //  relation
                    'price' => $faker->randomFloat(2, 20, 100),
                    'is_optional' => true,
                ]);
            }

            //  service
            if (!empty($branchServiceTypes)) {
                PackageExtra::create([
                    'package_id' => $package->id,
                    'type' => 'service',
                    'food_item_id' => null,
                    'branch_service_type_id' => $faker->randomElement($branchServiceTypes),
                    'name' => '', // relation
                    'price' => $faker->randomFloat(2, 50, 150),
                    'is_optional' => true,
                ]);
            }


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
