<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

Route::group([
    'middleware'=>['auth:sanctum', 'role:workshop'],
],function(){
});

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::resource('categories', CategoryController::class);
Route::apiResource('cars', CarController::class);




Route::get('/test-api', function () {
    return response()->json(['message' => 'API is working']);
});
