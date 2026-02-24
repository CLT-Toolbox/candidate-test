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
});

Route::middleware('auth')->resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);

Route::middleware('auth')->prefix('suppliers/{supplier}')->name('suppliers.')->group(function () {
    Route::resource('layups', CltLayupController::class)->except(['create', 'show', 'edit']);
    Route::prefix('layups/{layup}')->name('layups.')->group(function () {
        Route::resource('layers', CltLayerController::class)->except(['create', 'show', 'edit']);
    });
});

require __DIR__.'/auth.php';
