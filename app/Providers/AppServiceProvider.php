<?php

namespace App\Providers;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\Supplier;
use App\Policies\LayerPolicy;
use App\Policies\LayupPolicy;
use App\Policies\SupplierPolicy;
use App\Repositories\Contracts\LayerRepositoryInterface;
use App\Repositories\Contracts\LayupRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Eloquent\LayerRepository;
use App\Repositories\Eloquent\LayupRepository;
use App\Repositories\Eloquent\SupplierRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(LayupRepositoryInterface::class, LayupRepository::class);
        $this->app->bind(LayerRepositoryInterface::class, LayerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Layup::class, LayupPolicy::class);
        Gate::policy(Layer::class, LayerPolicy::class);
    }
}
