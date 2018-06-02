<?php

namespace App\DeviceActionInfo;

use App\RFDevice;

class RFDeviceActionInfo implements IActionInfo
{
    public static function providesInfoFor(): string
    {
        return RFDevice::class;
    }

    public function turnOn(RFDevice $rfDevice): array
    {
        return [
            'code' => $rfDevice->on_code,
            'pulse_length' => $rfDevice->pulse_length
        ];
    }

    public function turnOff(RFDevice $rfDevice): array
    {
        return [
            'code' => $rfDevice->off_code,
            'pulse_length' => $rfDevice->pulse_length
        ];
    }
}
