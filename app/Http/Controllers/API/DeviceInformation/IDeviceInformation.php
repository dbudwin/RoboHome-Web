<?php

namespace App\Http\Controllers\API\DeviceInformation;

use Illuminate\Http\JsonResponse;

interface IDeviceInformation
{
    public function info(int $deviceId, string $action): JsonResponse;
}
