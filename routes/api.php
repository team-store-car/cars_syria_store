<?php
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard')->group(function () {
    Route::apiResource('stores', StoreController::class);
});