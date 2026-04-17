<?php

namespace App\Providers;

use App\Contracts\Services\ExportServiceInterface;
use App\Contracts\Services\ImportServiceInterface;
use App\Contracts\Services\LayerServiceInterface;
use App\Contracts\Services\LayupServiceInterface;
use App\Contracts\Services\SupplierServiceInterface;
use App\Services\ExportService;
use App\Services\ImportService;
use App\Services\LayerService;
use App\Services\LayupService;
use App\Services\SupplierService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SupplierServiceInterface::class, SupplierService::class);
        $this->app->singleton(LayupServiceInterface::class, LayupService::class);
        $this->app->singleton(LayerServiceInterface::class, LayerService::class);
        $this->app->singleton(ImportServiceInterface::class, ImportService::class);
        $this->app->singleton(ExportServiceInterface::class, ExportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
