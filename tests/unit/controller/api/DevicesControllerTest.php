<?php

namespace Tests\Unit\Controller\Api;

use App\Http\Controllers\API\DeviceInformation\IDeviceInformation;
use App\Http\Globals\DeviceActions;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\JsonResponse;
use Mockery;
use Tests\Unit\Controller\Common\DevicesControllerTestCase;

class DevicesControllerTest extends DevicesControllerTestCase
{
    private $mockDeviceInformation;
    private $messageId;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockDeviceInformation = Mockery::mock(IDeviceInformation::class);

        $this->app->instance(IDeviceInformation::class, $this->mockDeviceInformation);

        $this->messageId = self::$faker->uuid;
    }

    public function testIndex_GivenUserExistsWithNoDevices_ReturnsJsonResponse(): void
    {
        $this->givenSingleUserExistsWithNoDevicesRegisteredWithApi();

        $response = $this->callDevices();

        $this->assertDiscoverAppliancesResponseWithoutDevice($response);
    }

    public function testIndex_GivenUserExistsWithDevices_ReturnsJsonResponse(): void
    {
        $device1Name = self::$faker->word();
        $device2Name = self::$faker->word();
        $device3Name = self::$faker->word();

        $this->givenSingleUserExistsWithDevicesRegisteredWithApi($device1Name, $device2Name, $device3Name);

        $response = $this->callDevices();

        $this->assertDiscoverAppliancesResponse($response, $device1Name, $device2Name, $device3Name);
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
        $user = $this->givenSingleUserExistsWithNoDevicesRegisteredWithApi();
        $device = $this->mockDeviceRecord(self::$faker->word(), $user);

        $this->givenDeviceIsRegisteredToUser($device, $user->user_id);

        $response = $this->callControl(DeviceActions::TURN_ON, $device->id);

        $this->assertControlConfirmation($response);
    }

    public function testTurnOn_GivenUserExistsWithDevice_CallsPublish(): void
    {
        $user = $this->givenSingleUserExistsWithNoDevicesRegisteredWithApi();
        $device = $this->mockDeviceRecord(self::$faker->word(), $user);

        $this->mockMessagePublisher();
        $this->givenDeviceIsRegisteredToUser($device, $user->user_id);

        $this->callControl(DeviceActions::TURN_ON, $device->id);
    }

    public function testTurnOn_GivenUserExistsWithNoDevices_Returns401(): void
    {
        $user = $this->givenSingleUserExistsWithNoDevicesRegisteredWithApi();
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->callControl(DeviceActions::TURN_ON, $deviceId);

        $response->assertStatus(401);
    }

    public function testTurnOff_GivenUserExistsWithDevice_ReturnsJsonResponse(): void
    {
        $user = $this->givenSingleUserExistsWithNoDevicesRegisteredWithApi();
        $device = $this->mockDeviceRecord(self::$faker->word(), $user);

        $this->givenDeviceIsRegisteredToUser($device, $user->user_id);

        $response = $this->callControl(DeviceActions::TURN_OFF, $device->id);

        $this->assertControlConfirmation($response);
    }

    public function testTurnOff_GivenUserExistsWithDevice_CallsPublish(): void
    {
        $user = $this->givenSingleUserExistsWithNoDevicesRegisteredWithApi();
        $device = $this->mockDeviceRecord(self::$faker->word(), $user);

        $this->mockMessagePublisher();
        $this->givenDeviceIsRegisteredToUser($device, $user->user_id);

        $this->callControl(DeviceActions::TURN_OFF, $device->id);
    }

    public function testTurnOff_GivenUserExistsWithNoDevices_Returns401(): void
    {
        $user = $this->givenSingleUserExistsWithNoDevicesRegisteredWithApi();
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->callControl(DeviceActions::TURN_OFF, $deviceId);

        $response->assertStatus(401);
    }

    public function testInfo_GivenUserExistsWithDevice_ReturnsJsonResponse(): void
    {
        $user = $this->givenSingleUserExists();
        $device = $this->mockDeviceRecord(self::$faker->word(), $user->user_id);

        $this->givenDoesUserOwnDevice($user, $device->id, true);

        $this->mockDeviceInformation->shouldReceive('info')->once()->andReturn(new JsonResponse());

        $response = $this->postJson('/api/devices/info', [
            'userId' => $user->user_id,
            'action' => self::$faker->word(),
            'deviceId' => $device->id
        ], []);

        $response->assertStatus(200);
    }

    public function testInfo_GivenRandomUserAndDevice_Returns401(): void
    {
        $userId = self::$faker->uuid();
        $user = $this->createUser($userId);
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->postJson('/api/devices/info', [
            'userId' => $userId,
            'action' => self::$faker->word(),
            'deviceId' => $deviceId
        ], []);

        $response->assertStatus(401);
    }

    private function givenSingleUserExistsWithNoDevicesRegisteredWithApi(): User
    {
        $user = $this->givenSingleUserExists();

        $this->registerUserWithApi($user);

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $user->user_id)->andReturn(Mockery::self())
            ->shouldReceive('first')->andReturn(Mockery::self())
            ->shouldReceive('getAttribute')->with('devices')->andReturn([]);

        $this->app->instance(User::class, $mockUserTable);

        return $user;
    }

    private function givenSingleUserExistsWithDevicesRegisteredWithApi(string $device1Name, string $device2Name, string $device3Name): void
    {
        $user = $this->givenSingleUserExistsWithDevices($device1Name, $device2Name, $device3Name);

        $this->registerUserWithApi($user);
    }

    private function registerUserWithApi(User $user): void
    {
//        $mockRequest = Mockery::mock(ILoginAuthenticator::class);
//        $mockRequest->shouldReceive('processApiLoginRequest')->withAnyArgs()->once()->andReturn($user);
//        $this->app->instance(ILoginAuthenticator::class, $mockRequest);
    }

    private function callDevices(): TestResponse
    {
        $response = $this->getJson('/api/devices', [
            'HTTP_Authorization' => 'Bearer ' . self::$faker->uuid(),
            'HTTP_Message_Id' => $this->messageId
        ]);

        return $response;
    }

    private function callControl(string $action, int $deviceId): TestResponse
    {
        $urlValidAction = strtolower($action);

        $response = $this->postJson('/api/devices/' . $urlValidAction, ['id' => $deviceId], [
            'HTTP_Authorization' => 'Bearer ' . self::$faker->uuid(),
            'HTTP_Message_Id' => $this->messageId
        ]);

        return $response;
    }

    private function assertDiscoverAppliancesResponseWithoutDevice(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'header' => [
                'messageId',
                'name',
                'namespace',
                'payloadVersion'
            ],
            'payload' => [
                'discoveredAppliances' => []
            ]
        ]);

        $response->assertSee($this->messageId);
    }

    private function assertDiscoverAppliancesResponse(TestResponse $response, string $device1Name, string $device2Name, string $device3Name): void
    {
        $response->assertJsonStructure([
            'header' => [
                'messageId',
                'name',
                'namespace',
                'payloadVersion'
            ],
            'payload' => [
                'discoveredAppliances' => [
                    [
                        'actions',
                        'additionalApplianceDetails',
                        'applianceId',
                        'friendlyName',
                        'friendlyDescription',
                        'isReachable',
                        'manufacturerName',
                        'modelName',
                        'version'
                    ],
                    [
                        'actions',
                        'additionalApplianceDetails',
                        'applianceId',
                        'friendlyName',
                        'friendlyDescription',
                        'isReachable',
                        'manufacturerName',
                        'modelName',
                        'version'
                    ],
                    [
                        'actions',
                        'additionalApplianceDetails',
                        'applianceId',
                        'friendlyName',
                        'friendlyDescription',
                        'isReachable',
                        'manufacturerName',
                        'modelName',
                        'version'
                    ]
                ]
            ]
        ]);

        $response->assertSee($this->messageId);
        $response->assertSee($device1Name);
        $response->assertSee($device2Name);
        $response->assertSee($device3Name);
    }

    private function assertControlConfirmation(TestResponse $response): void
    {
        $response->assertJsonStructure([
            'header' => [
                'messageId',
                'name',
                'namespace',
                'payloadVersion'
            ],
            'payload' => []
        ]);

        $response->assertSee($this->messageId);
    }
}
