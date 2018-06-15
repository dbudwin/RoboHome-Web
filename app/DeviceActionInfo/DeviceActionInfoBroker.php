<?php

namespace App\DeviceActionInfo;

use App\Device;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class DeviceActionInfoBroker implements IDeviceActionInfoBroker
{
    public function infoNeededToPerformDeviceAction(Device $device, string $action): JsonResponse
    {
        $specificDevice = $device->specificDevice();

        $deviceInfoClasses = app()->tagged('deviceActionInfoClasses');

        foreach ($deviceInfoClasses as $deviceInfoClass) {
            if ($deviceInfoClass->providesInfoFor() == get_class($specificDevice)) {
                return $this->infoForDeviceAction($deviceInfoClass, $specificDevice, $action);
            }
        }

        return response()->json(['error' => 'Device is not supported yet'], 400);
    }

    private function infoForDeviceAction(IActionInfo $deviceInfoClass, Model $specificDevice, string $action): JsonResponse
    {
        if ($this->deviceInfoClassSupportsAction($deviceInfoClass, $action)) {
            $response = (new $deviceInfoClass())->$action($specificDevice);

            return response()->json($response);
        }

        return response()->json(['error' => "Action '$action' not implemented for device"], 400);
    }

    private function deviceInfoClassSupportsAction(IActionInfo $infoProvider, string $action): bool
    {
        return method_exists($infoProvider, $action);
    }
}
