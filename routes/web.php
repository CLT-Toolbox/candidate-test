<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\ImportExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [SupplierController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Supplier CRUD - List & Create
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');

    // Export / Import - MUST BE BEFORE {supplier} parameter routes!
    Route::get('/suppliers/export-list', [ImportExportController::class, 'exportList'])->name('suppliers.export-list');
    Route::get('/suppliers/{supplier}/export', [ImportExportController::class, 'export'])->name('suppliers.export');
    Route::post('/suppliers/{supplier}/detect-conflicts', [ImportExportController::class, 'detectConflicts'])->name('suppliers.detect-conflicts');
    Route::post('/suppliers/{supplier}/import', [ImportExportController::class, 'import'])->name('suppliers.import');

    // Supplier CRUD - Show, Edit, Delete
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Layups (nested under Supplier)
    Route::post('/suppliers/{supplier}/layups', [CltLayupController::class, 'store'])->name('suppliers.layups.store');
    Route::get('/suppliers/{supplier}/layups/{layup}', [CltLayupController::class, 'show'])->name('suppliers.layups.show');
    Route::patch('/suppliers/{supplier}/layups/{layup}', [CltLayupController::class, 'update'])->name('suppliers.layups.update');
    Route::delete('/suppliers/{supplier}/layups/{layup}', [CltLayupController::class, 'destroy'])->name('suppliers.layups.destroy');

    // Layers (nested under Layup)
    Route::post('/suppliers/{supplier}/layups/{layup}/layers', [CltLayerController::class, 'store'])->name('suppliers.layups.layers.store');
    Route::patch('/suppliers/{supplier}/layups/{layup}/layers/{layer}', [CltLayerController::class, 'update'])->name('suppliers.layups.layers.update');
    Route::delete('/suppliers/{supplier}/layups/{layup}/layers/{layer}', [CltLayerController::class, 'destroy'])->name('suppliers.layups.layers.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
