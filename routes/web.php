<?php

Route::get('/', 'Web\LoginController@index')->name('index');
Route::get('/login', 'Web\LoginController@login')->name('login');
Route::get('/logout', 'Web\LoginController@logout')->name('logout');

Route::get('/devices', 'Web\DevicesController@devices')->name('devices');
Route::post('/devices/add', 'Web\DevicesController@add')->name('addDevice');
Route::get('/devices/delete/{deviceId}', 'Web\DevicesController@delete')->name('deleteDevice');
