<?php

use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api', 'scope:control');

Route::resource('/devices', 'API\DevicesController');
Route::post('/devices/turnon', 'API\DevicesController@turnOn')->name('turnOn')->middleware('scope:control');
Route::post('/devices/turnoff', 'API\DevicesController@turnOff')->name('turnOff')->middleware('scope:control');
Route::post('/devices/info', 'API\DevicesController@info')->name('info')->middleware('scope:info');
Route::post('/settings/mqtt', 'API\SettingsController@mqtt')->name('mqttSettings')->middleware('scope:info');
