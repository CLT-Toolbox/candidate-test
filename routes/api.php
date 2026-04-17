<?php

use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\LayerController;
use App\Http\Controllers\Api\LayupController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Support\Facades\Route;

Route::scopeBindings()->group(function (): void {
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('suppliers.layups', LayupController::class)->shallow();
    Route::apiResource('layups.layers', LayerController::class)->shallow();

    Route::post('suppliers/{supplier}/import', ImportController::class);
    Route::get('suppliers/{supplier}/export', ExportController::class);
});
