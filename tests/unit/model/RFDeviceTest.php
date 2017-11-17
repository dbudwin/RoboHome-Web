<?php

namespace Tests\Unit\Model;

use App\RFDevice;

class RFDeviceTest extends TestCaseWithRealDatabase
{
    public function testAdd_GivenRFDeviceAddedToDatabase_DatabaseOnlyHasOneRFDeviceRecord(): void
    {
        $onCode = self::$faker->randomNumber();
        $offCode = self::$faker->randomNumber();
        $pulseLength = self::$faker->randomNumber();
        $deviceId = self::$faker->randomNumber();

        $rfDevice = factory(RFDevice::class)->create([
            'on_code' => $onCode,
            'off_code' => $offCode,
            'pulse_length' => $pulseLength,
            'device_id' => $deviceId
        ]);

        $this->assertCount(1, RFDevice::all());
        $this->assertEquals($onCode, $rfDevice->on_code);
        $this->assertEquals($offCode, $rfDevice->off_code);
        $this->assertEquals($pulseLength, $rfDevice->pulse_length);
        $this->assertEquals($deviceId, $rfDevice->device_id);
    }
}
