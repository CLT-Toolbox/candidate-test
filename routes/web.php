<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\LayerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('suppliers/import', [SupplierController::class, 'importForm'])
            ->name('suppliers.import.form');
        Route::post('suppliers/import', [SupplierController::class, 'import'])
            ->name('suppliers.import');
        Route::get('suppliers/{supplier}/export', [SupplierController::class, 'export'])
            ->name('suppliers.export');
        
        Route::resource('suppliers', SupplierController::class);
        Route::resource('suppliers.layups', LayupController::class)->shallow();
        Route::resource('layups.layers', LayerController::class)->shallow();
    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';