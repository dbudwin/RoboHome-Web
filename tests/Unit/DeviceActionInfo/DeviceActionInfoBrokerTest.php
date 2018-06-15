<?php

namespace Tests\Unit\DeviceActionInfo;

use App\Device;
use App\DeviceActionInfo\DeviceActionInfoBroker;
use App\DeviceActionInfo\RFDeviceActionInfo;
use App\RFDevice;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class DeviceActionInfoBrokerTest extends TestCase
{
    public function testInfoNeededToPerformDeviceAction_GivenUnsupportedDeviceType_Returns400(): void
    {
        $deviceActionInfoBroker = new DeviceActionInfoBroker();

        $mockDevice = Mockery::mock(Device::class)->makePartial();
        $mockDevice->shouldReceive('specificDevice')->once()->andReturn(new FakeDeviceModel());

        $action = self::$faker->word();

        $response = $deviceActionInfoBroker->infoNeededToPerformDeviceAction($mockDevice, $action);

        $this->assertJsonResponse($response, json_encode(['error' => 'Device is not supported yet']), 400);
    }

    public function testInfoNeededToPerformDeviceAction_GivenRFDeviceAndUnsupportedAction_Returns400(): void
    {
        $deviceActionInfoBroker = new DeviceActionInfoBroker();

        $mockDevice = Mockery::mock(Device::class)->makePartial();
        $mockDevice->shouldReceive('specificDevice')->once()->andReturn(new RFDevice());

        $action = self::$faker->word();

        $response = $deviceActionInfoBroker->infoNeededToPerformDeviceAction($mockDevice, $action);

        $this->assertJsonResponse($response, json_encode(['error' => "Action '$action' not implemented for device"]), 400);
    }

    public function testInfoNeededToPerformDeviceAction_GivenRFDeviceAndSupportedAction_Returns200(): void
    {
        $deviceActionInfoBroker = new DeviceActionInfoBroker();

        $rfDevice = $this->makeRFDevice(self::$faker->randomNumber(), self::$faker->randomNumber(), self::$faker->randomNumber());
        $mockDevice = Mockery::mock(Device::class)->makePartial();
        $mockDevice->shouldReceive('specificDevice')->once()->andReturn($rfDevice);

        $action = $this->pickActionForDevice(RFDeviceActionInfo::class);

        $response = $deviceActionInfoBroker->infoNeededToPerformDeviceAction($mockDevice, $action);

        $this->assertEquals($response->getStatusCode(), 200);
    }

    private function pickActionForDevice(string $deviceActionInfoClass): string
    {
        $reflection = new ReflectionClass($deviceActionInfoClass);
        $public = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $static = $reflection->getMethods(ReflectionMethod::IS_STATIC);
        $actionMethods = array_diff($public, $static);

        $action = $actionMethods[array_rand($actionMethods)]->name;

        return $action;
    }
}

class FakeDeviceModel extends Model
{
}
