<?php

namespace Tests\Unit\Controller\API;

use App\Device;
use App\Http\Controllers\API\DeviceInformation\IDeviceInformation;
use App\Http\Globals\DeviceActions;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

        $this->messageId = self::$faker->uuid();
    }

    public function testIndex_GivenUserExistsWithNoDevices_ReturnsJsonResponse(): void
    {
        $mockUser = $this->createMockUser();
        $mockUser->shouldReceive('getAttribute')->with('devices')->once()->andReturn([]);

        $response = $this->callDevices($mockUser);

        $this->assertDiscoverAppliancesResponseWithoutDevice($response);
    }

    public function testIndex_GivenUserExistsWithDevices_ReturnsJsonResponse(): void
    {
        $numberOfDevices = self::$faker->numberBetween(1, 10);
        $devices = $this->createDevices($numberOfDevices);

        $mockUser = $this->createMockUser();
        $mockUser->shouldReceive('getAttribute')->with('devices')->once()->andReturn($devices);

        $response = $this->callDevices($mockUser);

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

    public function testAllDeviceActions_GivenUserExistsWithDevice_ReturnsJsonResponse(): void
    {
        foreach ($this->deviceActionsConstants() as $deviceAction) {
            $device = $this->createDevices()[0];
            $mockUser = $this->mockUserOwnsDevice($device->id, true);

            $this->mockMessagePublisher(1);

            $response = $this->callControl($mockUser, $deviceAction, $device->id);

            $this->assertControlConfirmation($response);
        }
    }

    public function testAllDeviceActions_GivenUserExistsWithDevice_CallsPublishSuccessfully_Returns200(): void
    {
        foreach ($this->deviceActionsConstants() as $deviceAction) {
            $device = $this->createDevices()[0];
            $mockUser = $this->mockUserOwnsDevice($device->id, true);

            $this->mockMessagePublisher(1);

            $response = $this->callControl($mockUser, $deviceAction, $device->id);

            $response->assertSuccessful();
        }
    }

    public function testAllDeviceActions_GivenUserExistsWithDevice_CallsPublishUnsuccessfully_Returns500(): void
    {
        foreach ($this->deviceActionsConstants() as $deviceAction) {
            $device = $this->createDevices()[0];
            $mockUser = $this->mockUserOwnsDevice($device->id, true);

            $this->mockMessagePublisher(1, false);

            $response = $this->callControl($mockUser, $deviceAction, $device->id);

            $response->assertStatus(500);
        }
    }

    public function testAllDeviceActions_GivenUserExistsWithNoDevices_Returns401(): void
    {
        foreach ($this->deviceActionsConstants() as $deviceAction) {
            $deviceId = self::$faker->randomDigit();
            $mockUser = $this->mockUserOwnsDevice($deviceId, false);

            $response = $this->callControl($mockUser, $deviceAction, $deviceId);

            $response->assertStatus(401);
        }
    }

    public function testInfo_GivenUserExistsWithDevice_ReturnsJsonResponse(): void
    {
        $device = $this->createDevices()[0];
        $mockUser = $this->mockUserOwnsDevice($device->id, true);
        $mockUserRepository = $this->givenGetCalledOnUserRepository($mockUser);

        $this->app->instance(UserRepository::class, $mockUserRepository);

        $this->mockDeviceInformation->shouldReceive('info')->once()->andReturn(new JsonResponse());

        $response = $this->callInfo($mockUser, $device->id);

        $response->assertSuccessful();
    }

    public function testInfo_GivenRandomUserThatExistsAndDevice_Returns401(): void
    {
        $deviceId = self::$faker->randomDigit();
        $mockUser = $this->mockUserOwnsDevice($deviceId, false);
        $mockUserRepository = $this->givenGetCalledOnUserRepository($mockUser);

        $this->app->instance(UserRepository::class, $mockUserRepository);

        $response = $this->callInfo($mockUser, $deviceId);

        $response->assertStatus(401);
    }

    public function testInfo_GivenNonexistentUserAndDevice_Returns404(): void
    {
        $mockUserRepository = $this->givenUserRepositoryThrowsException();

        $this->app->instance(UserRepository::class, $mockUserRepository);

        $response = $this->postJson('/api/devices/info', [
            'userId' => self::$faker->randomNumber(),
            'action' => self::$faker->word(),
            'deviceId' => self::$faker->randomDigit()
        ], []);

        $response->assertStatus(404);
    }

    private function createMockUser(): User
    {
        $user = $this->createUser();

        $mockUser = Mockery::mock(User::class);
        $mockUser
            ->shouldReceive('getAuthIdentifier')->andReturn($user->id)
            ->shouldReceive('getAttribute')->with('id')->andReturn($user->id);

        return $mockUser;
    }

    private function callDevices(User $user): TestResponse
    {
        $response = $this->actingAs($user, 'api')->getJson('/api/devices', [
            'HTTP_Authorization' => 'Bearer ' . self::$faker->uuid(),
            'HTTP_Message_Id' => $this->messageId
        ]);

        return $response;
    }

    private function callControl(User $user, string $action, int $deviceId): TestResponse
    {
        $urlValidAction = strtolower($action);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/devices/' . $urlValidAction, ['id' => $deviceId], [
                'HTTP_Authorization' => 'Bearer ' . self::$faker->uuid(),
                'HTTP_Message_Id' => $this->messageId
            ]);

        return $response;
    }

    private function callInfo(User $user, int $deviceId): TestResponse
    {
        $response = $this->postJson('/api/devices/info', [
            'userId' => $user->id,
            'action' => self::$faker->word(),
            'deviceId' => $deviceId
        ], []);

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

    private function assertDiscoverAppliancesResponse(TestResponse $response, Collection $devices): void
    {
        $appliances = [];

        for ($i = 0; $i < $devices->count(); $i++) {
            $appliances += [
                'actions',
                'additionalApplianceDetails',
                'applianceId',
                'friendlyName',
                'friendlyDescription',
                'isReachable',
                'manufacturerName',
                'modelName',
                'version'
            ];
        }

        $response->assertJsonStructure([
            'header' => [
                'messageId',
                'name',
                'namespace',
                'payloadVersion'
            ],
            'payload' => [
                'discoveredAppliances' => [
                    $appliances
                ]
            ]
        ]);

        $response->assertSee($this->messageId);

        foreach ($devices as $device) {
            $response->assertSee($device->name);
        }
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

    private function createDevices(int $numberOfDevices = 1): Collection
    {
        return factory(Device::class, $numberOfDevices)->make([
            'id' => self::$faker->randomNumber()
        ]);
    }

    private function mockUserOwnsDevice(int $deviceId, bool $userOwnsDevice): User
    {
        $mockUser = $this->createMockUser();
        $mockUser
            ->shouldReceive('ownsDevice')->with($deviceId)->once()->andReturn($userOwnsDevice)
            ->shouldReceive('token')->andReturn(self::$faker->uuid())
            ->shouldReceive('tokenCan')->andReturn(self::$faker->word());

        return $mockUser;
    }

    private function givenGetCalledOnUserRepository(User $user): UserRepository
    {
        $mockUserRepository = Mockery::mock(UserRepository::class);
        $mockUserRepository->shouldReceive('get')->once()->andReturn($user);

        return $mockUserRepository;
    }

    private function givenUserRepositoryThrowsException(): UserRepository
    {
        $mockUserRepository = Mockery::mock(UserRepository::class);
        $mockUserRepository->shouldReceive('get')->once()->andThrow(ModelNotFoundException::class);

        return $mockUserRepository;
    }

    private function deviceActionsConstants(): array
    {
        $deviceActionsClass = new \ReflectionClass(DeviceActions::class);
        $deviceActions = $deviceActionsClass->getConstants();

        return $deviceActions;
    }
}
