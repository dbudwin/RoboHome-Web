<?php

namespace App\Providers;

use App\DeviceActionInfo\DeviceActionInfoBroker;
use App\DeviceActionInfo\IDeviceActionInfoBroker;
use App\DeviceActionInfo\RFDeviceActionInfo;
use Illuminate\Support\ServiceProvider;

class DeviceInformationProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IDeviceActionInfoBroker::class, DeviceActionInfoBroker::class);

        $this->app->tag(
            [
                RFDeviceActionInfo::class,
            ],
            'deviceActionInfoClasses'
        );
    }
}
