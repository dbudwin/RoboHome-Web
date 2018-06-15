<?php

namespace App\DeviceActionInfo;

use App\Device;
use Illuminate\Http\JsonResponse;

interface IDeviceActionInfoBroker
{
    public function infoNeededToPerformDeviceAction(Device $device, string $action): JsonResponse;
}
