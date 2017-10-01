<?php

namespace App\Http\Controllers\API\DeviceInformation;

use Illuminate\Http\JsonResponse;

class ErrantDeviceInformation implements IDeviceInformation
{
    public function info(int $deviceId, string $action): JsonResponse
    {
        return response()->json(['error' => 'Bad Request'], 400);
    }
}
