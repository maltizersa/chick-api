<?php

use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', [APIController::class, 'hello']);

Route::post('/verifyEmail', [APIController::class, 'verifyEmail']);

Route::post('/register', [APIController::class, 'register']);

Route::post('/login', [APIController::class, 'login']);