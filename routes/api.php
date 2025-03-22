<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'=>'cars',
],function(){
});
Route::resource('categories', CategoryController::class);
Route::apiResource('cars', CarController::class);
