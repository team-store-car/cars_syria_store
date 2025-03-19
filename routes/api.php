<?php
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ProfileController;


Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/profile', [ProfileController::class, 'profile']);
});
Route::middleware(['auth:sanctum', RoleMiddleware::class . ':admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome Admin']);
    });
});

Route::middleware(['auth:sanctum', RoleMiddleware::class . ':workshop'])->group(function () {
    Route::get('/workshop/dashboard', function () {
        return response()->json(['message' => 'Welcome Workshop']);
    });
});
Route::get('/test-api', function () {
    return response()->json(['message' => 'API is working']);
});