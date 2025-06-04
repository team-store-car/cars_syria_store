<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CarOfferController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkshopAdController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\InspectionRequestController;
use App\Http\Controllers\CarRecommendationController;

use App\Http\Middleware\RoleMiddleware;


Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('stores', StoreController::class)->only(['index', 'show']);
Route::apiResource('cars', CarController::class)->only(['index', 'show']);
Route::get('/car-offers', [CarOfferController::class, 'index'])->name('car-offers.index');
Route::get('/car-offers/{offer}', [CarOfferController::class, 'show'])->name('car-offers.show');
Route::get('/stores/{store}/cars', [StoreController::class, 'cars'])->name('stores.cars');


Route::group([
    'middleware'=>['auth:sanctum'],
],function(){
    Route::apiResource('cars', CarController::class)->except(['index','show']);
    Route::post('/cars/{car}/images', [CarController::class, 'addImage'])->name('cars.addImage');
    Route::put('/cars/images/{image}', [CarController::class, 'updateImage'])->name('cars.updateImage');
    Route::delete('/cars/images/{image}', [CarController::class, 'deleteImage'])->name('cars.deleteImage');
    Route::post('/cars/{car}/offers', [CarOfferController::class, 'store'])->name('car-offers.create');
    Route::put('/car-offers/{offer}', [CarOfferController::class, 'update'])->name('car-offers.update');
    Route::delete('/car-offers/{offer}', [CarOfferController::class, 'destroy'])->name('car-offers.delete');
    Route::apiResource('stores', StoreController::class)->except(['index','show']);

});


Route::group([
    'middleware'=>['auth:sanctum','role:shop_manager,admin'],
],function(){
    Route::apiResource('categories', CategoryController::class)->except(['index','show']);

});
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
     
     // Neue Routen für Bildverwaltung
     Route::post('/workshop-ads/{workshopAd}/images', [WorkshopAdController::class, 'addImage'])->name('workshop-ads.images.store');
     Route::put('/workshop-ads/images/{image}', [WorkshopAdController::class, 'updateImage'])->name('workshop-ads.images.update');
     Route::delete('/workshop-ads/images/{image}', [WorkshopAdController::class, 'deleteImage'])->name('workshop-ads.images.destroy');
 });

     Route::get('/workshops', [WorkshopController::class, 'index']);

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
