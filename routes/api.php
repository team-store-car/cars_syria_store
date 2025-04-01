<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;



Route::get('/test-api', function () {
    return response()->json(['message' => 'API is working']);
});