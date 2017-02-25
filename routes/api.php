<?php

Route::resource('/devices', 'API\DevicesController');
Route::post('/devices/turnon', 'API\DevicesController@turnOn')->name('turnOn');
Route::post('/devices/turnoff', 'API\DevicesController@turnOff')->name('turnOff');
