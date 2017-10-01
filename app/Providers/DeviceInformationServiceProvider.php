<?php

namespace App\Providers;

use App\Http\Controllers\API\DeviceInformation\ErrantDeviceInformation;
use App\Http\Controllers\API\DeviceInformation\IDeviceInformation;
use App\Http\Controllers\API\DeviceInformation\RFDeviceInformation;
use App\Http\Globals\DeviceTypes;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class DeviceInformationServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot(): void
    {
        $this->app->call([$this, 'registerDeviceInformationTypes']);
    }

    public function registerDeviceInformationTypes(Request $request): void
    {
        if (!$request->has('deviceId')) {
            $this->app->bind(IDeviceInformation::class, ErrantDeviceInformation::class);
            return;
        }

        $deviceId = $request->get('deviceId');

        $deviceModel = app('App\Device');

        $device = $deviceModel->find($deviceId);

        if ($device === null) {
            $this->app->bind(IDeviceInformation::class, ErrantDeviceInformation::class);
            return;
        }

        $deviceType = $device->device_type_id;

        if ($deviceType === DeviceTypes::RF_DEVICE) {
            $this->app->bind(IDeviceInformation::class, RFDeviceInformation::class);
        }
    }

    public function provides(): array
    {
        return [IDeviceInformation::class];
    }
}
