<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Contracts\CltLayupRepositoryInterface;
use App\Repositories\Contracts\CltLayerRepositoryInterface;
use App\Repositories\Eloquent\SupplierRepository;
use App\Repositories\Eloquent\CltLayupRepository;
use App\Repositories\Eloquent\CltLayerRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(CltLayupRepositoryInterface::class, CltLayupRepository::class);
        $this->app->bind(CltLayerRepositoryInterface::class, CltLayerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
