<?php

Route::get('/', 'Web\LoginController@index')->name('index');
Route::get('/login', 'Web\LoginController@login')->name('login');
Route::get('/logout', 'Web\LoginController@logout')->name('logout');
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
