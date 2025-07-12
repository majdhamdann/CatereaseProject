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
        $serviceTypesIds = ServiceType::pluck('id');
        $occasionTypeIds = OccasionType::pluck('id');
        $categoryIds     = Category::pluck('id');

        $packageTemplates = [
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
                'prepayment_amount'      => 0.00,
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

            $branchServiceTypeIds = BranchServiceType::where('branch_id', $branch->id)
                ->pluck('id');
            if ($branchServiceTypeIds->isEmpty()) {
                continue;
            }

            $packagesPerBranch = rand(2, 4);

            for ($i = 0; $i < $packagesPerBranch; $i++) {
                $template          = $packageTemplates[array_rand($packageTemplates)];
                $randomOccasions   = $occasionTypeIds->random(rand(1, 2));
                $randomCategories  = $categoryIds->random(rand(1, 2));


                $package = Package::create([
                    'branch_id'                => $branch->id,
                    'branch_service_type_id'   => $branchServiceTypeIds->random(),
                    'name'                     => $template['name'] . ' ' . ($i + 1),
                    'description'              => $template['description'],
                    'photo'                    => 'placeholder.jpg',
                    'serves_count'             => rand(15, 50),
                    'base_price'               => $template['base_price'],
                    'cancellation_policy'      => $faker->randomElement([
                        'Cancelable up to 24 hours in advance',
                        'Non-refundable',
                        '50% refund if canceled 48 hours before',
                    ]),
                    'prepayment_required'      => $template['prepayment_required'],
                    'prepayment_amount'        => $template['prepayment_amount'],
                    'is_active'                => true,
                    'notes'                    => $faker->sentence(),
                    'max_extra_persons'        => $template['max_extra_persons'],
                    'price_per_extra_person'   => $template['price_per_extra_person'],
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ]);


                $package->categories()->attach($randomCategories);


                $package->occasionTypes()->attach($randomOccasions);


                $count = min(rand(2, 3), $branchServiceTypeIds->count());
                $package->extraServices()
                    ->attach($branchServiceTypeIds->random($count));
            }
        }
    }
}
