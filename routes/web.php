<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\WEBController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('email/{email}', [APIController::class, 'sendMail']);    

Route::get('/admin/login', [WEBController::class, 'showlogin']);

Route::post('/login', [WEBController::class, 'login']);

Route::get('/addhotel', [WEBController::class, "home"]);

Route::get('/hotel/approve/{id}', [WEBController::class, 'approve']);
Route::get('/hotel/deny/{id}', [WEBController::class, 'deny']);

Route::get('/hotelowners', [WEBController::class, "hotelowners"]);

Route::get('/logout', [WEBController::class, "logout"]);