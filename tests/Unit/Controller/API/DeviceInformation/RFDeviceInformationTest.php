<?php

namespace Tests\Unit\Controller\API\DeviceInformation;

use App\Http\Controllers\API\DeviceInformation\RFDeviceInformation;
use App\Http\Globals\DeviceActions;
use App\Repositories\RFDeviceRepository;
use App\RFDevice;
use Illuminate\Foundation\Testing\TestResponse;
use Mockery;
use Tests\TestCase;

class RFDeviceInformationTest extends TestCase
{
    private $onCode;
    private $offCode;
    private $pulseLength;

    public function setUp(): void
    {
        parent::setUp();

        $this->onCode = self::$faker->randomDigit();
        $this->offCode = self::$faker->randomDigit();
        $this->pulseLength = self::$faker->randomDigit();
    }

    public function testInfo_GivenTurnOnAction_ReturnsJsonResponseWithCorrectOnCode(): void
    {
        $response = $this->callInfo(DeviceActions::TURN_ON);

        $response->assertExactJson([
            'code' => $this->onCode,
            'pulse_length' => $this->pulseLength
        ]);
    }

    public function testInfo_GivenTurnOffAction_ReturnsJsonResponseWithCorrectOffCode(): void
    {
        $response = $this->callInfo(DeviceActions::TURN_OFF);

        $response->assertExactJson([
            'code' => $this->offCode,
            'pulse_length' => $this->pulseLength
        ]);
    }

    public function testInfo_GivenUnknownAction_Returns400(): void
    {
        $action = self::$faker->word();

        $response = $this->callInfo($action);

        $response->assertStatus(400);

        $response->assertExactJson([
            'error' => "Device does not support action '$action'"
        ]);
    }

    private function callInfo(string $action): TestResponse
    {
        $rfDevice = $this->makeRFDevice($this->onCode, $this->offCode, $this->pulseLength);

        $mockRfDeviceRepository = Mockery::mock(RFDeviceRepository::class);
        $mockRfDeviceRepository->shouldReceive('get')->with($rfDevice->device_id)->once()->andReturn($rfDevice);

        $rfDeviceInformation = new RFDeviceInformation($mockRfDeviceRepository);

        $response = $rfDeviceInformation->info($rfDevice->device_id, $action);

        return TestResponse::fromBaseResponse($response);
    }

    private function makeRFDevice(int $onCode, int $offCode, int $pulseLength): RFDevice
    {
        return factory(RFDevice::class)->make([
            'device_id' => self::$faker->randomNumber,
            'on_code' => $onCode,
            'off_code' => $offCode,
            'pulse_length' => $pulseLength,
        ]);
    }
}
