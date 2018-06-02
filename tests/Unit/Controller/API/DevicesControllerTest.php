<?php

namespace Tests\Unit\Controller\API;

use App\Device;
use App\DeviceActionInfo\IDeviceActionInfoBroker;
use App\Http\Globals\DeviceActions;
use App\Repositories\IDeviceRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestResponse;
use Laravel\Passport\Passport;
use Mockery;
use Tests\Unit\Controller\Common\DevicesControllerTestCase;
use Webpatser\Uuid\Uuid;

class DevicesControllerTest extends DevicesControllerTestCase
{
    private $mockDeviceRepository;
    private $mockUser;
    private $messageId;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockDeviceRepository = Mockery::mock(IDeviceRepository::class);
        $this->mockUser = $this->createMockUser();
        $this->messageId = self::$faker->uuid();

        $this->app->instance(IDeviceRepository::class, $this->mockDeviceRepository);
    }

    public function testIndex_GivenUserExistsWithNoDevices_ReturnsJsonResponse(): void
    {
        $this->mockUser->shouldReceive('getAttribute')->with('devices')->once()->andReturn([]);

        $response = $this->callDevices();

        $this->assertDiscoverAppliancesResponseWithoutDevice($response, 'DiscoverAppliancesResponse');
    }

    public function testIndex_GivenUserExistsWithDevices_ReturnsJsonResponse(): void
    {
        $numberOfDevices = self::$faker->numberBetween(1, 10);
        $devices = $this->createDevices($numberOfDevices);

        $this->mockUser->shouldReceive('getAttribute')->with('devices')->once()->andReturn($devices);

        $response = $this->callDevices();

        $this->assertDiscoverAppliancesResponse($response, $devices);
    }

    public function testIndex_GivenUserDoesNotExist_Returns401(): void
    {
        $response = $this->getJson('/api/devices', [
            'HTTP_Authorization' => 'Bearer ' . self::$faker->uuid(),
            'HTTP_Message_Id' => $this->messageId
        ]);

        $response->assertStatus(401);
    }

    public function testTurnOn_GivenUserExistsWithDevice_ReturnsJsonResponse(): void
    {
        $this->assertSuccessfulControlJsonResponse(DeviceActions::TURN_ON, 'TurnOnConfirmation');
    }

    public function testTurnOff_GivenUserExistsWithDevice_ReturnsJsonResponse(): void
    {
        $this->assertSuccessfulControlJsonResponse(DeviceActions::TURN_OFF, 'TurnOffConfirmation');
    }

    public function testTurnOn_GivenUserExistsWithDevice_CallsPublishSuccessfully_Returns200(): void
    {
        $this->assertPublishedCalledAndMessagePublished(DeviceActions::TURN_ON);
    }

    public function testTurnOff_GivenUserExistsWithDevice_CallsPublishSuccessfully_Returns200(): void
    {
        $this->assertPublishedCalledAndMessagePublished(DeviceActions::TURN_OFF);
    }

    public function testTurnOn_GivenUserExistsWithDevice_CallsPublishUnsuccessfully_Returns500(): void
    {
        $this->assertPublishedCalledAndMessageNotPublished(DeviceActions::TURN_ON);
    }

    public function testTurnOff_GivenUserExistsWithDevice_CallsPublishUnsuccessfully_Returns500(): void
    {
        $this->assertPublishedCalledAndMessageNotPublished(DeviceActions::TURN_OFF);
    }

    public function testTurnOn_GivenUserExistsWithNoDevices_Returns401(): void
    {
        $this->assertUnauthorizedWhenUserAttemptsToControlDeviceTheyDoNotOwn(DeviceActions::TURN_ON);
    }

    public function testTurnOff_GivenUserExistsWithNoDevices_Returns401(): void
    {
        $this->assertUnauthorizedWhenUserAttemptsToControlDeviceTheyDoNotOwn(DeviceActions::TURN_OFF);
    }

    public function testInfo_GivenUserExistsWithDeviceWithRandomScope_Returns400(): void
    {
        $deviceId = self::$faker->randomDigit();
        $action = self::$faker->word();
        $this->mockUser->shouldReceive('ownsDevice')->with($deviceId)->never();

        Passport::actingAs($this->mockUser, [self::$faker->word()]);

        $response = $this->callInfo($deviceId, $action);

        $response->assertStatus(400);
    }

    public function testInfo_GivenRandomUserThatExistsAndDeviceTheyDoNotOwn_Returns401(): void
    {
        $deviceUserDoesNotOwn = $this->createDevices()[0];
        $publicDeviceId = self::$faker->uuid();
        $action = self::$faker->word();
        $this->mockUserOwnsDevice($deviceUserDoesNotOwn->id, false);

        Passport::actingAs($this->mockUser, ['info']);

        $this->mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($publicDeviceId) {
            return $argument instanceof Uuid && $argument == Uuid::import($publicDeviceId);
        }))->once()->andReturn($deviceUserDoesNotOwn);

        $response = $this->callInfo($publicDeviceId, $action);

        $response->assertStatus(401);
    }

    public function testInfo_GivenUserExistsWithDevice_ReturnsJsonResponse(): void
    {
        $device = $this->createDevices()[0];
        $action = self::$faker->word();
        $this->mockUserOwnsDevice($device->id, true);

        Passport::actingAs($this->mockUser, ['info']);

        $mockDeviceActionInfoBroker = Mockery::mock(IDeviceActionInfoBroker::class);
        $mockDeviceActionInfoBroker
            ->shouldReceive('infoNeededToPerformDeviceAction')
            ->once()
            ->withArgs([$device, $action])
            ->andReturn(response()->json("test"));
        $this->app->instance(IDeviceActionInfoBroker::class, $mockDeviceActionInfoBroker);

        $this->mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
            return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
        }))->once()->andReturn($device);

        $response = $this->callInfo($device->public_id, $action);

        $response->assertSuccessful();
    }

    private function callDevices(): TestResponse
    {
        Passport::actingAs($this->mockUser, ['control']);

        $response = $this->getJson('/api/devices', [
            'HTTP_Authorization' => 'Bearer ' . self::$faker->uuid(),
            'HTTP_Message_Id' => $this->messageId
        ]);

        return $response;
    }

    private function callControl(string $action, string $publicDeviceId): TestResponse
    {
        $urlValidAction = strtolower($action);

        Passport::actingAs($this->mockUser, ['control']);

        $response = $this->postJson('/api/devices/control/' . $urlValidAction, ['publicDeviceId' => $publicDeviceId], [
            'HTTP_Authorization' => 'Bearer ' . self::$faker->uuid(),
            'HTTP_Message_Id' => $this->messageId
        ]);

        return $response;
    }

    private function callInfo(string $publicDeviceId, string $action): TestResponse
    {
        $response = $this->postJson('/api/devices/info', [
            'action' => $action,
            'publicDeviceId' => $publicDeviceId
        ]);

        return $response;
    }

    private function assertSuccessfulControlJsonResponse(string $action, string $name): void
    {
        $device = $this->createDevices()[0];
        $this->mockUserOwnsDevice($device->id, true);
        $this->mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
            return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
        }))->once()->andReturn($device);

        $this->mockMessagePublisher(1);

        $response = $this->callControl($action, $device->public_id);

        $this->assertControlConfirmation($response, $name);
    }

    private function assertPublishedCalledAndMessagePublished(string $action): void
    {
        $device = $this->createDevices()[0];
        $this->mockUserOwnsDevice($device->id, true);
        $this->mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
            return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
        }))->once()->andReturn($device);

        $this->mockMessagePublisher(1);

        $response = $this->callControl($action, $device->public_id);

        $response->assertSuccessful();
    }

    private function assertPublishedCalledAndMessageNotPublished(string $action): void
    {
        $device = $this->createDevices()[0];
        $this->mockUserOwnsDevice($device->id, true);
        $this->mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
            return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
        }))->once()->andReturn($device);

        $this->mockMessagePublisher(1, false);

        $response = $this->callControl($action, $device->public_id);

        $response->assertStatus(500);
    }

    private function assertUnauthorizedWhenUserAttemptsToControlDeviceTheyDoNotOwn(string $action): void
    {
        $device = $this->createDevices()[0];
        $this->mockUserOwnsDevice($device->id, false);
        $this->mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
            return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
        }))->once()->andReturn($device);

        $response = $this->callControl($action, $device->public_id);

        $response->assertStatus(401);
    }

    private function assertDiscoverAppliancesResponseWithoutDevice(TestResponse $response, string $name): void
    {
        $response->assertExactJson([
            'header' => [
                'messageId' => $this->messageId,
                'name' => $name,
                'namespace' => 'Alexa.ConnectedHome.Discovery',
                'payloadVersion' => '2'
            ],
            'payload' => [
                'discoveredAppliances' => []
            ]
        ]);
    }

    private function assertDiscoverAppliancesResponse(TestResponse $response, Collection $devices): void
    {
        $appliances = [];

        for ($i = 0; $i < $devices->count(); $i++) {
            $appliances[] = [
                'actions' => [DeviceActions::TURN_ON, DeviceActions::TURN_OFF],
                'additionalApplianceDetails' => [],
                'applianceId' => $devices[$i]->id,
                'friendlyName' => $devices[$i]->name,
                'friendlyDescription' => $devices[$i]->description,
                'isReachable' => true,
                'manufacturerName' => 'N/A',
                'modelName' => 'N/A',
                'version' => 'N/A'
            ];
        }

        $response->assertExactJson([
            'header' => [
                'messageId' => $this->messageId,
                'name' => 'DiscoverAppliancesResponse',
                'namespace' => 'Alexa.ConnectedHome.Discovery',
                'payloadVersion' => '2'
            ],
            'payload' => [
                'discoveredAppliances' => $appliances
            ]
        ]);
    }

    private function assertControlConfirmation(TestResponse $response, string $name): void
    {
        $response->assertExactJson([
            'header' => [
                'messageId' => $this->messageId,
                'name' => $name,
                'namespace' => 'Alexa.ConnectedHome.Control',
                'payloadVersion' => '2'
            ],
            'payload' => []
        ]);
    }

    private function createDevices(int $numberOfDevices = 1): Collection
    {
        return factory(Device::class, $numberOfDevices)->make([
            'id' => self::$faker->randomNumber()
        ]);
    }

    private function createMockUser(): User
    {
        $publicUserId = self::$faker->uuid();

        $mockUser = $mockUser = Mockery::mock(User::class)->makePartial();
        $mockUser->shouldReceive('getAttribute')->with('public_id')->andReturn($publicUserId);

        return $mockUser;
    }

    private function mockUserOwnsDevice(int $deviceId, bool $userOwnsDevice): void
    {
        $this->mockUser->shouldReceive('ownsDevice')->with($deviceId)->once()->andReturn($userOwnsDevice);
    }
}
