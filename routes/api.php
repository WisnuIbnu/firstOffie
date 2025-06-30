<?php

use App\Http\Controllers\Api\BookingTransactionController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\OfficeSpaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware('api_key', 'throttle:1,1')->group(function() {

    Route::get('/office/{officeSpace:slug}', [OfficeSpaceController::class, 'show']);
    Route::apiResource('/offices', OfficeSpaceController::class);


    Route::get('/city/{city:slug}', [CityController::class,'show']);
    Route::apiResource('/cities', CityController::class);

    Route::post('/booking-transaction',[BookingTransactionController::class,'store']);
    Route::post('/check-booking',[BookingTransactionController::class,'booking_details']);

});


