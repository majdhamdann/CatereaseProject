<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Branch;
use App\Models\ServiceType;
use App\Models\OccasionType;
use App\Models\Category;
use Faker\Factory as Faker;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        $branches = Branch::pluck('id');
        $serviceTypes = ServiceType::pluck('id');
        $occasionTypes = OccasionType::pluck('id');
        $categories = Category::pluck('id');


        $packageTypes = [
            [
                'name' => 'Ramadan Iftar Package',
                'description' => 'Complete meal package for Iftar during Ramadan',
                'base_price' => 120.00,
                'prepayment_required' => true,
                'prepayment_amount' => 60.00
            ],
            [
                'name' => 'Family Gathering Package',
                'description' => 'Ideal for large family gatherings',
                'base_price' => 250.00,
                'prepayment_required' => false
            ],
            [
                'name' => 'Kids Package',
                'description' => 'Special meals for kids with small toys',
                'base_price' => 80.00,
                'prepayment_required' => true,
                'prepayment_amount' => 30.00
            ],
            [
                'name' => 'Business Hosting Package',
                'description' => 'Suitable for meetings and conferences',
                'base_price' => 180.00,
                'prepayment_required' => true,
                'prepayment_amount' => 90.00
            ]
        ];

        foreach ($packageTypes as $packageData) {
            Package::create([
                'branch_id' => $branches->random(),
                'service_type_id' => $serviceTypes->random(),
                'occasion_type_id' => $occasionTypes->random(),
                'category_id' => $categories->random(),
                'name' => $packageData['name'],
                'description' => $packageData['description'],
                'photo' => 'packages' ,
                'base_price' => $packageData['base_price'],
                'cancellation_policy' => $faker->randomElement([
                    'Cancelable up to 24 hours in advance',
                    'Non-refundable',
                    '50% refund if canceled 48 hours before'
                ]),
                'prepayment_required' => $packageData['prepayment_required'] ?? false,
                'prepayment_amount' => $packageData['prepayment_amount'] ?? 0,
                'is_active' => true,
                'notes' => $faker->sentence(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

//        $this->command->info(count($packageTypes) . ' packages created successfully!');
    }
}
