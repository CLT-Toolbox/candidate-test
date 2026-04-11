<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Suppliers\LayerController;
use App\Http\Controllers\Suppliers\LayupController;
use App\Http\Controllers\Suppliers\SupplierController;
use App\Http\Controllers\Suppliers\SupplierTransferController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')
    ->scopeBindings()
    ->group(function (): void {
        Route::resource('suppliers', SupplierController::class);
        Route::resource('suppliers.layups', LayupController::class)
            ->except(['index', 'show']);
        Route::resource('suppliers.layups.layers', LayerController::class)
            ->except(['index', 'show']);

        Route::get('suppliers/{supplier}/export', [SupplierTransferController::class, 'export'])
            ->name('suppliers.export');
        Route::post('suppliers/{supplier}/import', [SupplierTransferController::class, 'preview'])
            ->name('suppliers.import.preview');
        Route::get('suppliers/{supplier}/import/conflicts', [SupplierTransferController::class, 'conflicts'])
            ->name('suppliers.import.conflicts');
        Route::post('suppliers/{supplier}/import/conflicts', [SupplierTransferController::class, 'resolve'])
            ->name('suppliers.import.resolve');
    });

require __DIR__.'/auth.php';
