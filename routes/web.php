<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierExchangeController;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', function () {
    return view('welcome');
});

// --- Dashboard & Features (Protected by Auth) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Grouping Fitur Supplier (Import/Export)
    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::get('/export/{id}', [SupplierExchangeController::class, 'export'])->name('export');
        Route::post('/import', [SupplierExchangeController::class, 'import'])->name('import');
    });

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';