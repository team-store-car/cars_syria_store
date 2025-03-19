<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/protected-route', function () {
//     return response()->json(['message' => 'Welcome!']);
// })->middleware('auth');