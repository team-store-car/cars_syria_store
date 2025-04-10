<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkshopAdController;

// قم بتجميع المسارات التي تتطلب مصادقة
Route::middleware(['auth:sanctum', 'role:workshop'])->group(function () {
     Route::post('/workshop-ads', [WorkshopAdController::class, 'store'])->name('workshop-ads.store');
     Route::put('/workshop-ads/{workshopAd}', [WorkshopAdController::class, 'update'])->name('workshop-ads.update');
     Route::delete('/workshop-ads/{workshopAd}', [WorkshopAdController::class, 'destroy'])->name('workshop-ads.destroy');
 });
 

