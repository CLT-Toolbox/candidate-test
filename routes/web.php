<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuppliersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LayupsController;
use App\Http\Controllers\LayersController;
use App\Http\Controllers\ImportController;

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

Route::middleware('auth')->group(function () {
    Route::resource('suppliers', SuppliersController::class);
});
Route::middleware('auth')->group(function () {
    Route::get('/suppliers/{supplier}/layups', [LayupsController::class, 'index'])->name('suppliers.layups');
    Route::post('/suppliers/{supplier}/layups', [LayupsController::class, 'store'])->name('suppliers.layups.store');
    Route::put('/suppliers/{supplier}/layups/{layup}', [LayupsController::class, 'update'])->name('suppliers.layups.update');
    Route::delete('/suppliers/{supplier}/layups/{layup}', [LayupsController::class, 'destroy'])->name('suppliers.layups.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('/suppliers/layups/{layup}/layers', [LayersController::class, 'index'])->name('layups.layers.index');
    Route::post('/suppliers/layups/{layup}/layers', [LayersController::class, 'store'])->name('layups.layers.store');
    Route::put('/suppliers/layups/{layup}/layers/{layer}', [LayersController::class, 'update'])->name('layups.layers.update');
    Route::delete('/suppliers/layups/{layup}/layers/{layer}', [LayersController::class, 'destroy'])->name('layups.layers.destroy');
});
Route::prefix('suppliers/{supplier}/import')->name('suppliers.import.')->group(function () {
    Route::post('/process', [ImportController::class, 'process'])->name('process');
    Route::post('/confirm', [ImportController::class, 'confirm'])->name('confirm');
});
require __DIR__.'/auth.php';
