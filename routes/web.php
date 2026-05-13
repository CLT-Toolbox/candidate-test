<?php

use App\Http\Controllers\LayerController;
use App\Http\Controllers\LayupController;
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

    // SUPPLIER
    Route::get('/suppliers', [SupplierController::class, 'index'])
        ->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])
        ->name('suppliers.create');
    Route::get('/suppliers/export/csv', [SupplierController::class, 'exportCsv'])
        ->name('suppliers.export.csv');
    Route::get('/suppliers/import', [SupplierController::class, 'importForm'])
        ->name('suppliers.import.form');
    Route::post('/suppliers/import', [SupplierController::class, 'importCsv'])
        ->name('suppliers.import.csv');
    Route::post('/suppliers', [SupplierController::class, 'store'])
        ->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])
        ->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])
        ->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])
        ->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])
        ->name('suppliers.destroy');

    // LAYUP
    Route::get('/layups', [LayupController::class, 'index'])
        ->name('layups.index');
    Route::get('/layups/create', [LayupController::class, 'create'])
        ->name('layups.create');
    Route::post('/layups', [LayupController::class, 'store'])
        ->name('layups.store');
    Route::get('/layups/{layup}/edit', [LayupController::class, 'edit'])
        ->name('layups.edit');
    Route::put('/layups/{layup}', [LayupController::class, 'update'])
        ->name('layups.update');
    Route::delete('/layups/{layup}', [LayupController::class, 'destroy'])
        ->name('layups.destroy');

    //LAYER 
    Route::get('/layers', [LayerController::class, 'index'])
        ->name('layers.index');
    Route::get('/layers/create', [LayerController::class, 'create'])
        ->name('layers.create');
    Route::post('/layers', [LayerController::class, 'store'])
        ->name('layers.store');
    Route::get('/layers/{layer}/edit', [LayerController::class, 'edit'])
        ->name('layers.edit');
    Route::put('/layers/{layer}', [LayerController::class, 'update'])
        ->name('layers.update');
    Route::delete('/layers/{layer}', [LayerController::class, 'destroy'])
        ->name('layers.destroy');
});

require __DIR__.'/auth.php';
