<?php

namespace App\Http\Controllers\API\DeviceInformation;

interface IDeviceInformation
{
    public function info($deviceId, $action);
}
