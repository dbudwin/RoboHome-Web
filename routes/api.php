<?php

Route::resource('/devices', 'API\DevicesController');
Route::post('/devices/turnon', 'API\DevicesController@turnOn')->name('turnOn');
Route::post('/devices/turnoff', 'API\DevicesController@turnOff')->name('turnOff');
Route::post('/devices/info', 'API\DevicesController@info')->name('info')->middleware('addContentLengthHeader');
