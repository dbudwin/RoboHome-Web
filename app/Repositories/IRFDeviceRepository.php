<?php

namespace App\Repositories;

use App\RFDevice;

interface IRFDeviceRepository
{
    public function create(int $deviceId, array $deviceProperties): RFDevice;
    public function get(int $deviceId): RFDevice;
    public function update(int $deviceId, array $deviceProperties): RFDevice;
}
