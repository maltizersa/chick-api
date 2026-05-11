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

Route::post('/forgot-password', [APIController::class, 'forgotPassword']);

Route::post('/reset-password', [APIController::class, 'resetPassword']);

Route::get('/fetch/hotels', [APIController::class, 'fetchHotels']);

Route::get('/fetch/hotel/details/{id}', [APIController::class, 'fetchdetails']);

Route::post('/bookings', [APIController::class, 'bookHotel']);

Route::get('/booking/{bookingID}', [APIController::class, 'getBookingDetails']);

Route::post('/addhotel', [APIController::class, 'addHotel']);
Route::post('/updateprofileimage', [APIController::class, 'updateProfileImage']);

Route::get('/fetchroomtypes/{id}', [APIController::class, 'fetchroomtypes']);

Route::get('/fetchmessages/{uid}', [APIController::class, 'fetchMessages']);

Route::get('/fetchchat/{uid}/{otherId}', [APIController::class, 'fetchChat']);
Route::post('/sendmessage', [APIController::class, 'sendMessage']);

Route::post('/markread', [APIController::class, 'markRead']);

Route::post('/deleteroom', [APIController::class, 'deleteRoom']);

Route::post('/updatehotel', [APIController::class, 'updateHotel']);

Route::post('/addroom', [APIController::class, 'addRoom']);

Route::get('/my-hotels/{ownerId}', [APIController::class, 'myHotels']);
Route::get('/my-book/{uid}', [APIController::class, 'myBookings']);

Route::get('/to-rate/{uid}', [APIController:: class, 'toRate']);

Route::post('rate-hotel', [APIController::class, 'rateHotel']);

Route::post('/changepass', [APIController::class, 'changepass']);

Route::post('/update-account',[APIController::class, 'updateaccount']);

Route::get('/notifcount/{uid}', [APIController::class, 'notifcount']);
Route::get('/getnotif/{uid}', [APIController::class, 'getnotif']);
Route::post('/createnotif', [APIController::class, 'createNotification']);

