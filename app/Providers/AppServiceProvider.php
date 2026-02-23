<?php

namespace App\Providers;

use App\Interfaces\CltLayupRepositoryInterface;
use App\Interfaces\SupplierRepositoryInteface;
use App\Repositories\CltLayupRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            SupplierRepositoryInteface::class,
            SupplierRepository::class,
            );

        $this->app->bind(
            CltLayupRepositoryInterface::class,
            CltLayupRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
    }
}
