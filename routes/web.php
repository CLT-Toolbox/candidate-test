<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\LayerController;
use App\Http\Controllers\LayupsDirectoryController;
use App\Http\Controllers\LayersDirectoryController;
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

    Route::get('layups', LayupsDirectoryController::class)->name('layups.index');
    Route::get('layers', LayersDirectoryController::class)->name('layers.index');

    Route::post('suppliers/{supplier}/check-conflicts', [SupplierController::class, 'checkConflicts'])->name('suppliers.check-conflicts');
    Route::get('suppliers/{supplier}/layups/{layup}/layers', [SupplierController::class, 'getLayupLayers'])->name('suppliers.layups.layers.list');

    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('suppliers/{supplier}/layups/import', [LayupController::class, 'import'])->name('suppliers.layups.import');
    Route::post('suppliers/{supplier}/layups/{layup}/sync-layers', [LayupController::class, 'syncLayers'])->name('suppliers.layups.sync-layers');
    Route::post('suppliers/{supplier}/layups/{layup}/duplicate', [LayupController::class, 'duplicate'])->name('suppliers.layups.duplicate');
    Route::resource('suppliers.layups', LayupController::class);

    Route::resource('suppliers.layups.layers', LayerController::class);
});

require __DIR__.'/auth.php';
