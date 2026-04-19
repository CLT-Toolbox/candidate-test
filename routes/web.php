<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDataController;
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
});

Route::resource('suppliers', SupplierController::class);
Route::resource('clt-layups', CltLayupController::class);
Route::resource('clt-layers', CltLayerController::class);


Route::get('/supplier/{id}/export/json', [SupplierDataController::class, 'exportJson'])->name('supplier.export.json');
Route::get('/supplier/{id}/export/csv', [SupplierDataController::class, 'exportCsv'])->name('supplier.export.csv');
Route::get('/supplier/{id}/export/excel', [SupplierDataController::class, 'exportExcel'])->name('supplier.export.excel');

Route::get('/suppliers/import/form', [SupplierDataController::class, 'importForm'])
    ->name('suppliers.import.form');

Route::post('/suppliers/{supplierId}/import', [SupplierDataController::class, 'import'])
    ->name('suppliers.import');

Route::post('/conflicts/resolve', [SupplierDataController::class, 'resolveConflict'])
    ->name('conflicts.resolve');

require __DIR__.'/auth.php';
