<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleModelPriceController;

//Route::view('/', 'welcome');
Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::resource('customers', CustomerController::class);
Route::resource('drivers', DriverController::class);
Route::resource('owners', OwnerController::class);
Route::resource('vehicles', VehicleController::class);
Route::resource('vehicle-model-prices', VehicleModelPriceController::class);

require __DIR__.'/auth.php';
