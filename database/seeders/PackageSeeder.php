<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Branch;
use App\Models\ServiceType;
use App\Models\OccasionType;
use App\Models\Category;
use App\Models\BranchServiceType;
use Faker\Factory as Faker;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $faker           = Faker::create();
        $branches        = Branch::all();
        $serviceTypes    = ServiceType::pluck('id');
        $occasionTypes   = OccasionType::pluck('id');
        $categories      = Category::pluck('id');

        $packageTypes = [
            [
                'name'                   => 'Ramadan Iftar Package',
                'description'            => 'Complete meal package for Iftar during Ramadan',
                'base_price'             => 120.00,
                'prepayment_required'    => true,
                'prepayment_amount'      => 60.00,
                'max_extra_persons'      => 10,
                'price_per_extra_person' => 15.00,
            ],
            [
                'name'                   => 'Family Gathering Package',
                'description'            => 'Ideal for large family gatherings',
                'base_price'             => 250.00,
                'prepayment_required'    => false,
                'max_extra_persons'      => 15,
                'price_per_extra_person' => 20.00,
            ],
            [
                'name'                   => 'Kids Package',
                'description'            => 'Special meals for kids with small toys',
                'base_price'             => 80.00,
                'prepayment_required'    => true,
                'prepayment_amount'      => 30.00,
                'max_extra_persons'      => 5,
                'price_per_extra_person' => 10.00,
            ],
            [
                'name'                   => 'Business Hosting Package',
                'description'            => 'Suitable for meetings and conferences',
                'base_price'             => 180.00,
                'prepayment_required'    => true,
                'prepayment_amount'      => 90.00,
                'max_extra_persons'      => 8,
                'price_per_extra_person' => 25.00,
            ],
        ];

        foreach ($branches as $branch) {
            $packagesPerBranch = rand(2, 4);


            $branchServiceTypeIds = BranchServiceType::where('branch_id', $branch->id)
                ->pluck('id')
                ->all();

            for ($i = 0; $i < $packagesPerBranch; $i++) {
                $packageData      = $packageTypes[array_rand($packageTypes)];
                $serviceTypeId    = $serviceTypes->random();
                $randomCategories = $categories->random(rand(1, 2));

                $package = Package::create([
                    'branch_id'              => $branch->id,
                    'branch_service_type_id' => $serviceTypeId,
                    'name'                   => $packageData['name'].' '.($i+1),
                    'description'            => $packageData['description'],
                    'photo'                  => 'dd',
                    'serves_count'           => rand(15, 50),
                    'base_price'             => $packageData['base_price'],
                    'cancellation_policy'    => $faker->randomElement([
                        'Cancelable up to 24 hours in advance',
                        'Non-refundable',
                        '50% refund if canceled 48 hours before'
                    ]),
                    'prepayment_required'    => $packageData['prepayment_required'] ?? false,
                    'prepayment_amount'      => $packageData['prepayment_amount'] ?? 0,
                    'is_active'              => true,
                    'notes'                  => $faker->sentence(),
                    'max_extra_persons'      => $packageData['max_extra_persons'],
                    'price_per_extra_person' => $packageData['price_per_extra_person'],
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);


                $package->categories()->attach($randomCategories);


                $occasionsToAttach = $occasionTypes->random(rand(1, 4));
                if (! is_array($occasionsToAttach)) {
                    $occasionsToAttach = [$occasionsToAttach];
                }
                foreach ($occasionsToAttach as $occId) {
                    $package->occasionTypes()->attach($occId);
                }


                if (! empty($branchServiceTypeIds)) {
                    $servicesToAttach = (array) array_rand(
                        array_flip($branchServiceTypeIds),
                        min(3, count($branchServiceTypeIds))
                    );
                    foreach ($servicesToAttach as $srvId) {
                        $package->extraServices()->attach($srvId);
                    }
                }
            }
        }
    }
}
