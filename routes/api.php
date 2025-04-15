<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillSyncController;

Route::middleware('api')->group(function () {
    Route::post('/sync-bills', [BillSyncController::class, 'sync']);
});
