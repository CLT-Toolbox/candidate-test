<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierExportController;
use App\Http\Controllers\SupplierImportController;
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

    Route::get("/suppliers", [SupplierController::class, 'index'])->name('suppliers');
    Route::get("/suppliers/create", [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post("/suppliers", [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get("/suppliers/{supplier}", [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get("/suppliers/{supplier}/edit", [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put("/suppliers/{supplier}", [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete("/suppliers/{supplier}", [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Add layups route
    Route::post("/suppliers/{supplier}/layups", [SupplierController::class, 'storeLayup'])->name('suppliers.layups.store');
    Route::put("/suppliers/{supplier}/layups/{layup}", [SupplierController::class, 'updateLayup'])->name('suppliers.layups.update');
    Route::delete("/suppliers/{supplier}/layups/{layup}", [SupplierController::class, 'destroyLayup'])->name('suppliers.layups.destroy');

    // Import/Export routes
    Route::get("/suppliers/{supplier}/export", [SupplierExportController::class, 'export'])->name('suppliers.export');
    Route::get("/suppliers/{supplier}/import", [SupplierImportController::class, 'showForm'])->name('suppliers.import.form');
    Route::post("/suppliers/{supplier}/import/upload", [SupplierImportController::class, 'upload'])->name('suppliers.import.upload');
    Route::get("/suppliers/{supplier}/import/conflicts", [SupplierImportController::class, 'showConflicts'])->name('suppliers.import.conflicts');
    Route::post("/suppliers/{supplier}/import/resolve", [SupplierImportController::class, 'resolveConflicts'])->name('suppliers.import.resolve');
    Route::post("/suppliers/{supplier}/import/json", [SupplierImportController::class, 'importJson'])->name('suppliers.import.json');
});

require __DIR__.'/auth.php';

