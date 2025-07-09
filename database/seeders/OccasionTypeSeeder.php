<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OccasionType;

class OccasionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occasionTypes = [
            [
                'name' => 'Wedding',
                'description' => 'Weddings and marriage celebrations'
            ],
            [
                'name' => 'Engagement',
                'description' => 'Engagement parties'
            ],
            [
                'name' => 'Birthday',
                'description' => 'Birthday celebrations'
            ],
            [
                'name' => 'Ramadan Iftar',
                'description' => 'Ramadan Iftar gatherings'
            ],
            [
                'name' => 'Business Meeting',
                'description' => 'Business meetings and events'
            ],
            [
                'name' => 'Graduation',
                'description' => 'Graduation parties'
            ]
        ];

        foreach ($occasionTypes as $type) {
            OccasionType::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
