<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\LayerController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Supplier Routes
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::get('/suppliers/import-form', [SupplierController::class, 'importForm'])->name('suppliers.import-form');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::post('/suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::patch('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('/suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');

    // Layup Routes (nested under Supplier)
    Route::get('/suppliers/{supplier}/layups', [LayupController::class, 'index'])->name('suppliers.layups.index');
    Route::get('/suppliers/{supplier}/layups/create', [LayupController::class, 'create'])->name('suppliers.layups.create');
    Route::post('/suppliers/{supplier}/layups', [LayupController::class, 'store'])->name('suppliers.layups.store');
    Route::get('/suppliers/{supplier}/layups/{layup}', [LayupController::class, 'show'])->name('suppliers.layups.show');
    Route::get('/suppliers/{supplier}/layups/{layup}/edit', [LayupController::class, 'edit'])->name('suppliers.layups.edit');
    Route::patch('/suppliers/{supplier}/layups/{layup}', [LayupController::class, 'update'])->name('suppliers.layups.update');
    Route::delete('/suppliers/{supplier}/layups/{layup}', [LayupController::class, 'destroy'])->name('suppliers.layups.destroy');

    // Layer Routes (nested under Layup)
    Route::get('/suppliers/{supplier}/layups/{layup}/layers', [LayerController::class, 'index'])->name('suppliers.layups.layers.index');
    Route::get('/suppliers/{supplier}/layups/{layup}/layers/create', [LayerController::class, 'create'])->name('suppliers.layups.layers.create');
    Route::post('/suppliers/{supplier}/layups/{layup}/layers', [LayerController::class, 'store'])->name('suppliers.layups.layers.store');
    Route::get('/suppliers/{supplier}/layups/{layup}/layers/{layer}', [LayerController::class, 'show'])->name('suppliers.layups.layers.show');
    Route::get('/suppliers/{supplier}/layups/{layup}/layers/{layer}/edit', [LayerController::class, 'edit'])->name('suppliers.layups.layers.edit');
    Route::patch('/suppliers/{supplier}/layups/{layup}/layers/{layer}', [LayerController::class, 'update'])->name('suppliers.layups.layers.update');
    Route::delete('/suppliers/{supplier}/layups/{layup}/layers/{layer}', [LayerController::class, 'destroy'])->name('suppliers.layups.layers.destroy');
});

require __DIR__.'/auth.php';
