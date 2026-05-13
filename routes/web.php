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

Route::middleware('auth')->scopeBindings()->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/layups', [LayupController::class, 'catalog'])->name('layups.catalog');
    Route::get('/layers', [LayerController::class, 'catalog'])->name('layers.catalog');

    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers-export', [SupplierController::class, 'exportIndex'])->name('suppliers.export.index');
    Route::get('suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::post('suppliers/{supplier}/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::get('suppliers/{supplier}/conflicts', [SupplierController::class, 'conflicts'])->name('suppliers.conflicts');
    Route::post('suppliers/{supplier}/conflicts', [SupplierController::class, 'applyConflicts'])->name('suppliers.conflicts.apply');

    Route::resource('suppliers.layups', LayupController::class);
    Route::post('suppliers/{supplier}/layups/{layup}/duplicate', [LayupController::class, 'duplicate'])->name('suppliers.layups.duplicate');
    Route::resource('suppliers.layups.layers', LayerController::class);
});

require __DIR__.'/auth.php';
