<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([

            'Restaurant_id' => '1',

            'location' => 'The most famous restaurants and the most delicious foods ',

            'description' =>' this branch is ...........',
            'photo' =>'logBranch.png',
            'Manager_id'=>1,
            'created_at'=>now(),
            'updated_at'=>now()

        ]);


    }
}
