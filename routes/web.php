<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerController;

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

require __DIR__.'/auth.php';
