<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\City;
use App\Models\BranchDeliveryArea;

class BranchDeliveryAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $branches = Branch::all();

        $cities = \App\Models\City::take(3)->get();

        foreach ($branches as $branch) {
            foreach ($cities as $city) {

                $exists = BranchDeliveryArea::where('branch_id', $branch->id)
                    ->where('city_id', $city->id)
                    ->exists();

                if (!$exists) {
                    BranchDeliveryArea::create([
                        'branch_id'      => $branch->id,
                        'city_id'        => $city->id,
                        'delivery_price' => rand(5, 25),
                    ]);
                }
            }
        }


    }
}
