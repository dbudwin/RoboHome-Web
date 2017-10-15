<?php

Route::get('/', function () {
    return view('home');
});

Auth::routes();

Route::get('/devices', 'Web\DevicesController@devices')->name('devices');
Route::post('/devices/add', 'Web\DevicesController@add')->name('addDevice');
Route::get('/devices/delete/{id}', 'Web\DevicesController@delete')->name('deleteDevice');
Route::post('/devices/update/{id}', 'Web\DevicesController@update')->name('updateDevice');
Route::post('/devices/{action}/{id}', 'Web\DevicesController@handleControlRequest')
    ->where(['action' => '[a-z]+'])
    ->name('handleControlRequest');
