<?php

namespace App\Providers;

use App\Repositories\Contracts\LayerRepositoryInterface;
use App\Repositories\Contracts\LayupRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\LayerRepository;
use App\Repositories\LayupRepository;
use App\Repositories\SupplierRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(LayupRepositoryInterface::class, LayupRepository::class);
        $this->app->bind(LayerRepositoryInterface::class, LayerRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
