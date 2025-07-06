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
        FoodCategorySeeder::class,
        CategorySeeder::class,
        FoodItemSeeder::class,

        AddressSeeder::class,
        OrderSeeder::class,
        OrderDetailSeeder::class,
        FeedBackTypeSeeder::class,

        ServiceTypeSeeder::class,
        BranchServiceTypeSeeder::class,
        OrderServiceTypeSeeder::class,

        DeliveryPersonSeeder::class,
        DeliverySeeder::class,
        DeliveryTrackingSeeder::class,
    ]);

    }
}
