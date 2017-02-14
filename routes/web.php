<?php

Route::get('/', 'LoginController@index')->name('index');
Route::get('/login', 'LoginController@login')->name('login');
Route::get('/logout', 'LoginController@logout')->name('logout');

Route::get('/devices', 'DevicesController@devices')->name('devices');
Route::post('/devices/add', 'DevicesController@add')->name('addDevice');
Route::get('/devices/delete/{deviceId}', 'DevicesController@delete')->name('deleteDevice');
