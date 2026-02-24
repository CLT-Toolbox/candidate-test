<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show');

    Route::post('/clt-layups', [CltLayupController::class, 'store'])->name('clt-layups.store');
    Route::get('/suppliers/{id}/export-layups', [CltLayupController::class, 'export'])->name('clt-layups.export');
    Route::post('/clt-layups/import', [CltLayupController::class, 'import'])->name('clt-layups.import');
    Route::post('/clt-layups/import/resolve', [CltLayupController::class, 'resolveImport'])->name('clt-layups.import.resolve');
    Route::put('/clt-layups/{id}', [CltLayupController::class, 'update'])->name('clt-layups.update');
    Route::delete('/clt-layups/{id}', [CltLayupController::class, 'destroy'])->name('clt-layups.destroy');
    Route::get('/clt-layups/{id}', [CltLayupController::class, 'show'])->name('clt-layups.show');

    Route::post('/clt-layups/{id}/layers', [CltLayerController::class, 'store'])->name('clt-layups.layers.store');
    Route::post('/clt-layups/{id}/batch-update', [CltLayerController::class, 'update'])->name('clt-layups.batch-update');
    Route::delete('/clt-layers/{id}', [CltLayerController::class, 'destroy'])->name('clt-layers.destroy');
});

require __DIR__.'/auth.php';
