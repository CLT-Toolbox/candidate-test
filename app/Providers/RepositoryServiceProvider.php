<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\SupplierRepositoryInterface::class,
            \App\Repositories\SupplierRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\CltLayupRepositoryInterface::class,
            \App\Repositories\CltLayupRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\CltLayerRepositoryInterface::class,
            \App\Repositories\CltLayerRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
