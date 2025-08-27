<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\City;
use App\Models\District;
use App\Models\BranchDeliveryArea;

class BranchDeliveryAreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();


        $cities = City::with('districts')->take(3)->get();

        foreach ($branches as $branch) {
            foreach ($cities as $city) {
                foreach ($city->districts as $district) {

                    $exists = BranchDeliveryArea::where('branch_id', $branch->id)
                        ->where('city_id', $city->id)
                        ->where('district_id', $district->id)
                        ->exists();

                    if (!$exists) {
                        BranchDeliveryArea::create([
                            'branch_id'      => $branch->id,
                            'city_id'        => $city->id,
                            'district_id'    => $district->id,
                            'delivery_price' => rand(5, 25),
                        ]);
                    }
                }
            }
        }
    }
}
