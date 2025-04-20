<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;




Route::get('/test-api', function () {
    return response()->json(['message' => 'API is working']);
});
