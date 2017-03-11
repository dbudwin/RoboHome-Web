<?php

namespace App\Http\Controllers\API\DeviceInformation;

use App\RFDevice;

class RFDeviceInformation implements IDeviceInformation
{
    private $rfDeviceModel;

    public function __construct(RFDevice $rfDeviceModel)
    {
        $this->rfDeviceModel = $rfDeviceModel;
    }

    public function info($deviceId, $action)
    {
        $rfDevice = $this->rfDeviceModel->where('device_id', $deviceId)->first();

        $response = null;

        $action = strtolower($action);

        if ($action === 'turnon') {
            $response = [
                'code' => $rfDevice->on_code
            ];
        } elseif ($action === 'turnoff') {
            $response = [
                'code' => $rfDevice->off_code
            ];
        }

        if ($response === null) {
            return response()->json(['error' => "Device does not support action '$action'"], 400);
        }

        return response()->json($response);
    }
}
