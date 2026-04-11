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
use App\Repositories\Eloquent\EloquentLayerRepository;
use App\Repositories\Eloquent\EloquentLayupRepository;
use App\Repositories\Eloquent\EloquentSupplierRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SupplierRepositoryInterface::class, EloquentSupplierRepository::class);
        $this->app->bind(LayupRepositoryInterface::class, EloquentLayupRepository::class);
        $this->app->bind(LayerRepositoryInterface::class, EloquentLayerRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(Layup::class, LayupPolicy::class);
        Gate::policy(Layer::class, LayerPolicy::class);
    }
}
