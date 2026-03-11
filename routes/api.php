<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\LayerController;

Route::apiResource('suppliers', SupplierController::class);
Route::get('suppliers/{supplier}/export', [SupplierController::class, 'export']);
Route::post('suppliers/{supplier}/import', [SupplierController::class, 'import']);

Route::apiResource('layups', LayupController::class);
Route::apiResource('layers', LayerController::class);
