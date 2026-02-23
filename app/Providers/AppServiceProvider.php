<?php

namespace App\Providers;

use App\Interfaces\SupplierRepositoryInteface;
use App\Repositories\SupplierRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useTailwind();
    }
}
