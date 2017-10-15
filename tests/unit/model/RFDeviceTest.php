<?php

namespace Tests\Unit\Model;

use App\RFDevice;

class RFDeviceTest extends TestCaseWithRealDatabase
{
    public function testAdd_GivenRFDeviceAddedToDatabase_DatabaseOnlyHasOneRFDeviceRecord(): void
    {
        $rfDevice = new RFDevice();
        $onCode = self::$faker->randomNumber();
        $offCode = self::$faker->randomNumber();
        $pulseLength = self::$faker->randomNumber();
        $deviceId = self::$faker->randomNumber();

        $rfDevice = $rfDevice->add($onCode, $offCode, $pulseLength, $deviceId);

        $this->assertCount(1, RFDevice::all());
        $this->assertEquals($onCode, $rfDevice->on_code);
        $this->assertEquals($offCode, $rfDevice->off_code);
        $this->assertEquals($pulseLength, $rfDevice->pulse_length);
        $this->assertEquals($deviceId, $rfDevice->device_id);
    }
}
