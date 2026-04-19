<?php

namespace App\Providers;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Policies\CltLayerPolicy;
use App\Policies\CltLayupPolicy;
use App\Policies\SupplierPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(CltLayup::class, CltLayupPolicy::class);
        Gate::policy(CltLayer::class, CltLayerPolicy::class);

        Route::bind('layup', function (string $value, $route) {
            $supplier = $route->parameter('supplier');
            if (! $supplier instanceof Supplier) {
                $supplier = Supplier::findOrFail($supplier);
            }

            return CltLayup::where('supplier_id', $supplier->id)
                ->where('id', $value)
                ->firstOrFail();
        });

        Route::bind('layer', function (string $value, $route) {
            $layup = $route->parameter('layup');
            if (! $layup instanceof CltLayup) {
                $layup = CltLayup::findOrFail($layup);
            }

            return CltLayer::where('clt_layup_id', $layup->id)
                ->where('id', $value)
                ->firstOrFail();
        });
    }
}
