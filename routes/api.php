<?php

use Illuminate\Http\Request;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api', 'scope:control');

Route::resource('/devices', 'API\DevicesController');
Route::post('/devices/control/{action}', 'API\DevicesController@control')->name('control')->middleware('scope:control');
Route::post('/devices/info', 'API\DevicesController@info')->name('info')->middleware('scope:info');
Route::get('/settings/mqtt', 'API\SettingsController@mqtt')->name('mqttSettings')->middleware('scope:info');
Route::get('/users/publicId', 'API\UsersController@publicId')->name('publicId')->middleware('scope:info');
