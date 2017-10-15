<?php

namespace App\Repositories;

use App\RFDevice;

class RFDeviceRepository implements IRFDeviceRepository
{
    public function create(int $deviceId, array $deviceProperties): RFDevice
    {
        $rfDevice = new RFDevice();

        $this->setProperties($rfDevice, $deviceProperties);

        $rfDevice->device_id = $deviceId;

        $rfDevice->save();

        return $rfDevice;
    }

    public function get(int $deviceId): RFDevice
    {
        return RFDevice::where('device_id', $deviceId)->firstOrFail();
    }

    public function update(int $deviceId, array $deviceProperties): RFDevice
    {
        $rfDevice = $this->get($deviceId);

        $this->setProperties($rfDevice, $deviceProperties);

        $rfDevice->save();

        return $rfDevice;
    }

    private function setProperties(RFDevice $rfDevice, array $deviceProperties): void
    {
        $rfDevice->on_code = $deviceProperties['on_code'];
        $rfDevice->off_code = $deviceProperties['off_code'];
        $rfDevice->pulse_length = $deviceProperties['pulse_length'];
    }
}
