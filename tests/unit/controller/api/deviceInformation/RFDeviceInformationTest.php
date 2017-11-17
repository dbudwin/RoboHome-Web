<?php

namespace Tests\Unit\Controller\Api\DeviceInformation;

use App\Http\Controllers\API\DeviceInformation\RFDeviceInformation;
use App\Http\Globals\DeviceActions;
use App\Repositories\RFDeviceRepository;
use App\RFDevice;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\TestCase;

class RFDeviceInformationTest extends TestCase
{
    private $onCode;
    private $offCode;

    public function setUp(): void
    {
        parent::setUp();

        $this->onCode = self::$faker->randomDigit();
        $this->offCode = self::$faker->randomDigit();
    }

    public function testInfo_GivenTurnOnAction_ReturnsJsonResponseWithCorrectOnCode(): void
    {
        $action = DeviceActions::TURN_ON;

        $response = $this->callInfo($action);

        $result = json_decode($response->getContent(), true);

        $this->assertEquals($result['code'], $this->onCode);
    }

    public function testInfo_GivenTurnOffAction_ReturnsJsonResponseWithCorrectOffCode(): void
    {
        $action = DeviceActions::TURN_OFF;

        $response = $this->callInfo($action);

        $result = json_decode($response->getContent(), true);

        $this->assertEquals($result['code'], $this->offCode);
    }

    public function testInfo_GivenUnknownAction_Returns400(): void
    {
        $action = self::$faker->word();

        $response = $this->callInfo($action);

        $result = json_decode($response->getContent(), true);

        $this->assertEquals($response->status(), 400);
        $this->assertEquals($result['error'], "Device does not support action '$action'");
    }

    private function callInfo(string $action): JsonResponse
    {
        $rfDevice = $this->createRFDevice($this->onCode, $this->offCode);

        $mockRfDeviceRepository = Mockery::mock(RFDeviceRepository::class);
        $mockRfDeviceRepository->shouldReceive('get')->with($rfDevice->device_id)->once()->andReturn($rfDevice);

        $rfDeviceInformation = new RFDeviceInformation($mockRfDeviceRepository);

        $response = $rfDeviceInformation->info($rfDevice->device_id, $action);

        return $response;
    }

    private function createRFDevice(int $onCode, int $offCode): RFDevice
    {
        $rfDevice = factory(RFDevice::class)->make([
            'device_id' => self::$faker->randomNumber,
            'on_code' => $onCode,
            'off_code' => $offCode
        ]);

        return $rfDevice;
    }
}
