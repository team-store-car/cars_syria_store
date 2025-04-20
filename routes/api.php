<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkshopAdController;
use App\Http\Controllers\WorkshopController;
use App\Http\Middleware\RoleMiddleware;

Route::group([
    'middleware'=>['auth:sanctum', 'role:workshop'],
],function(){
});

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::resource('categories', CategoryController::class);
Route::apiResource('cars', CarController::class);



// قم بتجميع المسارات التي تتطلب مصادقة
Route::middleware(['auth:sanctum', 'role:workshop'])->group(function () {
     Route::post('/workshop-ads', [WorkshopAdController::class, 'store'])->name('workshop-ads.store');
     Route::put('/workshop-ads/{workshopAd}', [WorkshopAdController::class, 'update'])->name('workshop-ads.update');
     Route::delete('/workshop-ads/{workshopAd}', [WorkshopAdController::class, 'destroy'])->name('workshop-ads.destroy');
 });

 Route::middleware('auth:sanctum')->group(function () {

    Route::post('/workshops', [WorkshopController::class, 'store']);


    Route::middleware('role:workshop')->group(function () {
         Route::put('/workshops/{workshop}', [WorkshopController::class, 'update']);
         Route::delete('/workshops/{workshop}', [WorkshopController::class, 'destroy']);
            });
});
Route::get('/test-api', function () {
    return response()->json(['message' => 'API is working']);
});
