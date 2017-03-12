<?php

namespace App\Providers;

use App\Http\Globals\DeviceTypes;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class DeviceInformationServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->app->call([$this, 'registerDeviceInformationTypes']);
    }

    public function registerDeviceInformationTypes(Request $request)
    {
        if (!$request->has('deviceId')) {
            return;
        }

        $deviceId = $request->get('deviceId');

        $deviceModel = app('App\Device');

        $device = $deviceModel->find($deviceId);

        if ($device === null) {
            return;
        }

        $deviceType = $device->device_type_id;

        $deviceInformationInterface = 'App\Http\Controllers\API\DeviceInformation\IDeviceInformation';

        if ($deviceType === DeviceTypes::RF_DEVICE) {
            $this->app->bind($deviceInformationInterface, 'App\Http\Controllers\API\DeviceInformation\RFDeviceInformation');
        }
    }
}
