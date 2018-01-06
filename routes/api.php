<?php

use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api', 'scope:control');

Route::resource('/devices', 'API\DevicesController');
Route::post('/devices/turnon', 'API\DevicesController@turnOn')->name('turnOn');
Route::post('/devices/turnoff', 'API\DevicesController@turnOff')->name('turnOff');
Route::post('/devices/info', 'API\DevicesController@info')->name('info');
