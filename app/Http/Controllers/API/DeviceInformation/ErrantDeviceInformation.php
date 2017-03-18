<?php

namespace App\Http\Controllers\API\DeviceInformation;

class ErrantDeviceInformation implements IDeviceInformation
{
    public function info($deviceId, $action)
    {
        return response()->json(['error' => 'Bad Request'], 400);
    }
}
