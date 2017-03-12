<?php

namespace Tests\Unit\Controller\Api\DeviceInformation;

use App\Http\Controllers\API\DeviceInformation\RFDeviceInformation;
use App\Http\Globals\DeviceActions;
use App\RFDevice;
use Mockery;
use Tests\TestCase;

class RFDeviceInformationTest extends TestCase
{
    private $onCode;
    private $offCode;

    public function setUp()
    {
        parent::setUp();

        $this->onCode = self::$faker->randomDigit();
        $this->offCode = self::$faker->randomDigit();
    }

    public function testInfo_GivenTurnOnAction_ReturnsJsonResponseWithCorrectOnCode()
    {
        $action = DeviceActions::TURN_ON;

        $response = $this->callInfo($action);

        $result = json_decode($response->getContent(), true);

        $this->assertEquals($result['code'], $this->onCode);
    }

    public function testInfo_GivenTurnOffAction_ReturnsJsonResponseWithCorrectOffCode()
    {
        $action = DeviceActions::TURN_OFF;

        $response = $this->callInfo($action);

        $result = json_decode($response->getContent(), true);

        $this->assertEquals($result['code'], $this->offCode);
    }

    public function testInfo_GivenUnknownAction_Returns400()
    {
        $action = self::$faker->word();

        $response = $this->callInfo($action);

        $result = json_decode($response->getContent(), true);

        $this->assertEquals($response->status(), 400);
        $this->assertEquals($result['error'], "Device does not support action '$action'");
    }

    private function callInfo($action)
    {
        $rfDevice = $this->createRFDevice($this->onCode, $this->offCode);
        $rfDeviceInformation = $this->givenRFDeviceInformation($rfDevice);

        $response = $rfDeviceInformation->info($rfDevice->device_id, $action);

        return $response;
    }

    private function givenRFDeviceInformation($rfDevice)
    {
        $mockRFDeviceTable = Mockery::mock(RFDevice::class);
        $mockRFDeviceTable
            ->shouldReceive('where')->with('device_id', $rfDevice->device_id)->andReturn(Mockery::self())
            ->shouldReceive('first')->andReturn($rfDevice);

        $rfDeviceInformation = new RFDeviceInformation($mockRFDeviceTable);

        return $rfDeviceInformation;
    }

    private function createRFDevice($onCode, $offCode)
    {
        $rfDevice = new RFDevice();

        $rfDevice->on_code = $onCode;
        $rfDevice->off_code = $offCode;
        $rfDevice->pulse_length = self::$faker->randomDigit();
        $rfDevice->device_id = self::$faker->randomDigit();

        return $rfDevice;
    }
}
