<?php

namespace Tests\Unit\Model;

use App\Device;
use App\RFDevice;

class DeviceTest extends ModelTestCase
{
    public function testAdd_GivenDeviceAddedToDatabase_DatabaseOnlyHasOneDeviceRecord()
    {
        $device = new Device();
        $name = self::$faker->word();
        $description = self::$faker->sentence();
        $type = self::$faker->randomDigit();
        $userId = self::$faker->randomDigit();

        $device = $device->add($name, $description, $type, $userId);

        $this->assertCount(1, Device::all());
        $this->assertEquals($name, $device->name);
        $this->assertEquals($description, $device->description);
        $this->assertEquals($type, $device->device_type_id);
        $this->assertEquals($userId, $device->user_id);
    }

    public function testRFDevice_GivenRFDeviceAddedToDatabase_FoundRFDeviceMatches()
    {
        $device = new Device();
        $name = self::$faker->word();
        $description = self::$faker->sentence();
        $type = self::$faker->randomDigit();
        $userId = self::$faker->randomDigit();

        $addedDevice = $device->add($name, $description, $type, $userId);

        $rfDevice = new RFDevice();
        $onCode = self::$faker->randomNumber();
        $offCode = self::$faker->randomNumber();
        $pulseLength = self::$faker->randomNumber();
        $deviceId = $addedDevice->id;

        $rfDevice = $rfDevice->add($onCode, $offCode, $pulseLength, $deviceId);

        $foundRfDevice = $addedDevice->rfDevice;

        $this->assertEquals($rfDevice->on_code, $foundRfDevice->on_code);
        $this->assertEquals($rfDevice->off_code, $foundRfDevice->off_code);
        $this->assertEquals($rfDevice->pulse_length, $foundRfDevice->pulse_length);
        $this->assertEquals($rfDevice->device_id, $foundRfDevice->device_id);
    }
}
