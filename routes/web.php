<?php

Route::get('/', function () {
    return view('home');
});

Auth::routes(['verify' => true]);

Route::middleware(['verified'])->group(function () {
    Route::get('/devices', 'Web\DevicesController@devices')->name('devices');
    Route::post('/devices/add', 'Web\DevicesController@add')->name('addDevice');
    Route::get('/devices/delete/{publicDeviceId}', 'Web\DevicesController@delete')->name('deleteDevice');
    Route::put('/devices/update/{publicDeviceId}', 'Web\DevicesController@update')->name('updateDevice');
    Route::post('/devices/{action}/{publicDeviceId}', 'Web\DevicesController@handleControlRequest')->name('handleControlRequest');
});
