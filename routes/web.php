<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
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

    Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('/supplier', [SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/{supplier}', [SupplierController::class, 'edit'])->name('supplier.edit');
    Route::get('/supplier/{supplier}/show', [SupplierController::class, 'show'])->name('supplier.show');
    Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
    Route::get('/supplier/{supplier}/export', [SupplierController::class, 'export'])->name('supplier.export');

    Route::get('/clt-layup', [CltLayupController::class, 'index'])->name('clt-layup.index');
    Route::post('/clt-layup', [CltLayupController::class, 'store'])->name('clt-layup.store');
    Route::get('/clt-layup/{cltLayup}', [CltLayupController::class, 'edit'])->name('clt-layup.edit');
    Route::get('/clt-layup/{cltLayup}/show', [CltLayupController::class, 'show'])->name('clt-layup.show');
    Route::delete('/clt-layup/{cltLayup}', [CltLayupController::class, 'destroy'])->name('clt-layup.destroy');

    Route::get('/clt-layer', [CltLayerController::class, 'index'])->name('clt-layer.index');
    Route::post('/clt-layer', [CltLayerController::class, 'store'])->name('clt-layer.store');
    Route::get('/clt-layer/{cltLayer}', [CltLayerController::class, 'edit'])->name('clt-layer.edit');
    Route::delete('/clt-layer/{cltLayer}', [CltLayerController::class, 'destroy'])->name('clt-layer.destroy');
});

require __DIR__ . '/auth.php';
