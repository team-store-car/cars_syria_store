<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkshopAdController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\InspectionRequestController;
use App\Http\Controllers\CarRecommendationController;

use App\Http\Middleware\RoleMiddleware;


Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('cars', CarController::class)->only(['index', 'show']);


Route::group([
    'middleware'=>['auth:sanctum'],
],function(){
    Route::apiResource('cars', CarController::class)->except(['index','show']);
});
Route::group([
    'middleware'=>['auth:sanctum','role:admin'],
],function(){
    Route::apiResource('categories', CategoryController::class)->except(['index','show']);
});

// Route::middleware('auth:sanctum')->group(function () {

// });
// Route::post('cars',[CarController::class,'store'])->name('cars.store');

Route::group([
    'middleware'=>['auth:sanctum','role:admin'],
],function(){
    Route::apiResource('categories', CategoryController::class)->except(['index','show']);
});



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




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/inspection-requests', [InspectionRequestController::class, 'store'])->name('inspection-requests.store');
    Route::delete('/inspection-requests/{id}', [InspectionRequestController::class, 'destroy'])->name('inspection-requests.destroy');

});


Route::post('/car-recommendations', [CarRecommendationController::class, 'getRecommendations'])
->name('api.v1.car-recommendations.get');



Route::get('/questions', [QuestionController::class, 'index']);
