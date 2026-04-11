<?php

namespace App\Providers;

use App\Http\Repositories\CltLayerRepository;
use App\Http\Repositories\CltLayupRepository;
use App\Http\Repositories\Contracts\CltLayerRepositoryInterface;
use App\Http\Repositories\Contracts\CltLayupRepositoryInterface;
use App\Http\Repositories\Contracts\SupplierRepositoryInterface;
use App\Http\Repositories\SupplierRepository;
use Illuminate\Support\ServiceProvider;

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
