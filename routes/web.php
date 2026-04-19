<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\CltLayerController;

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

    Route::resource('suppliers', SupplierController::class);
    
    Route::resource('suppliers.layups', CltLayupController::class)->scoped();
    
    Route::scopeBindings()->group(function () {
        Route::post('suppliers/{supplier}/layups/{layup}/duplicate', [CltLayupController::class, 'duplicate'])
            ->name('suppliers.layups.duplicate');
        
        Route::resource('suppliers.layups.layers', CltLayerController::class)->scoped();
        Route::post('suppliers/{supplier}/layups/{layup}/layers/reorder', [CltLayerController::class, 'reorder'])
            ->name('suppliers.layups.layers.reorder');
    });

    // Import/Export Routes
    Route::get('suppliers/{supplier}/export', [ImportExportController::class, 'export'])->name('suppliers.export');
    Route::post('suppliers/{supplier}/import/analyze', [ImportExportController::class, 'importAnalyze'])->name('suppliers.import.analyze');
    Route::post('suppliers/{supplier}/import/confirm', [ImportExportController::class, 'importConfirm'])->name('suppliers.import.confirm');
});

require __DIR__.'/auth.php';
