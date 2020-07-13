<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Auth')->group(function () {
    Route::post('/register', 'AuthController@registerUser');
    Route::post('/login', 'AuthController@userLogin');
    Route::post('/logout', 'AuthController@userLogout')->middleware('auth');
});

Route::namespace('Auth')->group(function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/getUser', 'UserController@getUser');
        Route::patch('/updateUser', 'UserController@updateUser');
        Route::patch('/updateUserPassword', 'UserController@updateUserPassword');
    });
});

Route::namespace('Auth')->group(function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::prefix('reservation')->group(function () {
            Route::get('/getReservations', 'ReservationController@getReservations');
            Route::prefix('{room}')->group(function () {
                Route::post('/reserve', 'ReservationController@createReservation');
            });
            Route::prefix('{reservation}')->group(function () {
                Route::get('/', 'ReservationController@getReservation');
                Route::post('/makePayment', 'ReservationController@makePayment');
                Route::patch('/verifyPayment/{reference}', 'ReservationController@verifyPayment');
                Route::patch('/', 'ReservationController@updateReservation');
                Route::delete('/', 'ReservationController@deleteReservation');
            });
        });
    });
    Route::patch('/reservation/{reservation}/checkin', 'ReservationController@userCheckin');
    Route::patch('/reservation/{reservation}/checkout', 'ReservationController@userCheckout');
});

Route::prefix('payments')->group(function() {
    Route::get('/', 'PaymentController@getPayments');
    Route::get('/{payment}', 'PaymentController@fetchPayment');
    Route::delete('/{payment}', 'PaymentController@deletePayment');
});

Route::prefix('room')->group(function () {
    Route::post('/create', 'RoomController@createRoom');
    Route::get('/get_all_rooms', 'RoomController@getRooms');
    Route::get('/get_available_rooms', 'RoomController@getAvailableRooms');
    Route::get('/get_a_room/{id}', 'RoomController@getRoom');
    Route::patch('{id}', 'RoomController@updateRoom');
    Route::delete('{id}', 'RoomController@deleteRoom');
});
