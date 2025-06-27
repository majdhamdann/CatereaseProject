<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\AddressRepositoryInterface;
use App\Repositories\AddressRepository;
use App\Repositories\Contracts\BranchRepositoryInterface;
use App\Repositories\BranchRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
