<?php

namespace Tests\Unit\DeviceActionInfo;

use App\DeviceActionInfo\RFDeviceActionInfo;
use App\RFDevice;
use Tests\TestCase;

class RFDeviceActionInfoTest extends TestCase
{
    private $rfDeviceActionInfo;
    private $onCode;
    private $offCode;
    private $pulseLength;

    public function setUp(): void
    {
        parent::setUp();

        $this->rfDeviceActionInfo = new RFDeviceActionInfo();
        $this->onCode = self::$faker->randomNumber();
        $this->offCode = self::$faker->randomNumber();
        $this->pulseLength = self::$faker->randomNumber();
    }

    public function testProvidesInfoFor_ReturnsRFDeviceClass(): void
    {
        $actual = $this->rfDeviceActionInfo->providesInfoFor();

        $this->assertEquals(RFDevice::class, $actual);
    }

    public function testTurnOn_GivenRFDevice_ReturnsJsonResponseWithCorrectProperties(): void
    {
        $rfDevice = $this->makeRFDevice($this->onCode, $this->offCode, $this->pulseLength);

        $actual = $this->rfDeviceActionInfo->turnOn($rfDevice);

        $this->assertEquals([
            'code' => $this->onCode,
            'pulse_length' => $this->pulseLength
        ], $actual);
    }

    public function testTurnOff_GivenRFDevice_ReturnsJsonResponseWithCorrectProperties(): void
    {
        $rfDevice = $this->makeRFDevice($this->onCode, $this->offCode, $this->pulseLength);

        $actual = $this->rfDeviceActionInfo->turnOff($rfDevice);

        $this->assertEquals([
            'code' => $this->offCode,
            'pulse_length' => $this->pulseLength
        ], $actual);
    }
}
