<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/devices', 'API\DevicesController');
Route::post('/devices/turnon', 'API\DevicesController@turnOn')->name('turnOn');
Route::post('/devices/turnoff', 'API\DevicesController@turnOff')->name('turnOff');
Route::post('/devices/info', 'API\DevicesController@info')->name('info');
