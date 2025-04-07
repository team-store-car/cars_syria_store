<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkshopAdController;


Route::post('/workshopads', [WorkshopAdController::class, 'store'])
     ->name('store');