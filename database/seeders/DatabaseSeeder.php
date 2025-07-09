<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\FoodItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
        RoleSeeder::class,
        UserSeeder::class,
        cityseeder::class,
        resturantseeder::class,

        BranchSeeder::class,
        CategorySeeder::class,
        FoodCategorySeeder::class,
        FoodItemSeeder::class,
            ServiceTypeSeeder::class,
        OccasionTypeSeeder::class,
        PackageSeeder::class,
        //CartItemSeeder::class,
        PackageItemSeeder::class,

        AddressSeeder::class,
        OrderSeeder::class,
        OrderDetailSeeder::class,
        FeedBackTypeSeeder::class,


        BranchServiceTypeSeeder::class,
        OrderServiceTypeSeeder::class,

        DeliveryPersonSeeder::class,
        DeliverySeeder::class,
        DeliveryTrackingSeeder::class,
    ]);

    }
}
