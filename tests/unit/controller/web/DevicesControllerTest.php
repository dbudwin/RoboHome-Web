<?php

namespace Tests\Unit\Controller\Web;

use App\Device;
use App\RFDevice;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Mockery;
use Tests\Unit\Controller\Common\DevicesControllerTestCase;

class DevicesControllerTest extends DevicesControllerTestCase
{
    public function testDevices_GivenUserNotLoggedIn_RedirectToIndex(): void
    {
        $response = $this->get('/devices');

        $this->assertRedirectedToRouteWith302($response, '/');
        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }

    public function testDevices_GivenUserLoggedIn_ViewContainsUsersName(): void
    {
        $user = $this->createUser(self::$faker->uuid());

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $user->user_id)->andReturn(Mockery::self())
            ->shouldReceive('first')->andReturn(Mockery::self())
            ->shouldReceive('getAttribute')->with('name')->andReturn($user->name)
            ->shouldReceive('getAttribute')->with('devices')->andReturn([]);

        $this->app->instance(User::class, $mockUserTable);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])->get('/devices');

        $response->assertSee($user->name . '\'s Controllable Devices');
        $response->assertStatus(200);
    }

    public function testDevices_GivenUserLoggedIn_ViewContainsUsersDevices(): void
    {
        $device1Name = self::$faker->word();
        $device2Name = self::$faker->word();
        $device3Name = self::$faker->word();

        $htmlAttributeName = self::$faker->word();
        $htmlAttributeValue = self::$faker->randomDigit();

        $user = $this->givenSingleUserExistsWithDevicesContainingHtmlAttibutes($device1Name, $device2Name, $device3Name, $htmlAttributeName, $htmlAttributeValue);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])->get('/devices');

        $htmlAttribute = 'data-device-' . $htmlAttributeName . '=' . $htmlAttributeValue;

        $response->assertSee($htmlAttribute);
        $response->assertSee($device1Name);
        $response->assertSee($device2Name);
        $response->assertSee($device3Name);
        $response->assertStatus(200);
    }

    public function testAdd_GivenPostedData_RedirectToDevices(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceName = self::$faker->word();
        $device = $this->mockDeviceRecord($deviceName, $user->user_id);

        $response = $this->addDeviceForUser($device, $user);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testAdd_GivenSingleUserExists_CallsAddForModels(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceName = self::$faker->name();
        $device = $this->mockDeviceRecord($deviceName, $user->user_id);

        $this->addDeviceForUser($device, $user);
    }

    public function testAdd_GivenSingleUserExists_SessionContainsSuccessMessage(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceName = self::$faker->name();
        $device = $this->mockDeviceRecord($deviceName, $user->user_id);

        $response = $this->addDeviceForUser($device, $user);

        $response->assertSessionHas('alert-success');
    }

    public function testDelete_GivenUserDoesNotOwnDevice_RedirectToDevices(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->get('/devices/delete/' . $deviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testDelete_GivenUserDoesNotOwnDevice_SessionContainsErrorMessage(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->get('/devices/delete/' . $deviceId);

        $response->assertSessionHas('alert-danger');
    }

    public function testDelete_GivenUserOwnsDevice_RedirectToDevices(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = $this->givenUserOwnsDeviceForDeletion($user);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->get('/devices/delete/' . $deviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testDelete_GivenUserOwnsDevice_SessionContainsSuccessMessage(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = $this->givenUserOwnsDeviceForDeletion($user);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->get('/devices/delete/' . $deviceId);

        $response->assertSessionHas('alert-success');
    }

    public function testUpdate_GivenUserDoesNotOwnDevice_RedirectToDevices(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->post('/devices/update/' . $deviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testUpdate_GivenUserDoesNotOwnDevice_SessionContainsErrorMessage(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->post('/devices/update/' . $deviceId);

        $response->assertSessionHas('alert-danger');
    }

    public function testUpdate_GivenUserOwnsDevice_SessionContainsSuccessMessage(): void
    {
        list($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength) = $this->parametersForUpdatingDevice();
        $this->givenUserOwnsDeviceForUpdating($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength);

        $response = $this->callUpdate($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength);

        $response->assertSessionHas('alert-success');
    }

    public function testUpdate_GivenUserOwnsDevice_RedirectToDevices(): void
    {
        list($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength) = $this->parametersForUpdatingDevice();
        $this->givenUserOwnsDeviceForUpdating($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength);

        $response = $this->callUpdate($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testUpdate_GivenUserOwnsDevice_ValuesForDeviceChanged(): void
    {
        list($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength) = $this->parametersForUpdatingDevice();

        $originalDeviceName = self::$faker->word();
        $originalDeviceDescription = self::$faker->sentence();
        $originalOnCode = self::$faker->randomNumber();
        $originalOffCode = self::$faker->randomNumber();
        $originalPulseLength = self::$faker->randomNumber();

        $device = $this->givenUserOwnsDeviceForUpdating($user, $deviceId, $originalDeviceName, $originalDeviceDescription, $originalOnCode, $originalOffCode, $originalPulseLength);

        $this->assertDevicePropertiesMatch($device, $originalDeviceName, $originalDeviceDescription, $originalOnCode, $originalOffCode, $originalPulseLength);

        $response = $this->callUpdate($user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength);

        $this->assertDevicePropertiesMatch($device, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength);
        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testHandleControlRequest_GivenUserExistsWithDevice_CallsPublish(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();
        $action = self::$faker->word();

        $mockUserRecord = $this->givenDoesUserOwnDevice($user, $deviceId, true);
        $mockUserRecord->shouldReceive('getAttribute')->with('user_id')->once()->andReturn($user->user_id);

        $this->mockMessagePublisher();

        $response = $this->callControl($user->user_id, $action, $deviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testHandleControlRequest_GivenUserExistsWithNoDevices_PublishIsNotCalled(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();
        $action = self::$faker->word();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);
        $this->mockMessagePublisher(0);

        $response = $this->callControl($user->user_id, $action, $deviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testHandleControlRequest_GivenUserExistsWithNoDevices_SessionContainsErrorMessage(): void
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();
        $action = self::$faker->word();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->callControl($user->user_id, $action, $deviceId);

        $response->assertSessionHas('alert-danger');
    }

    private function callControl(string $userId, string $action, int $deviceId): TestResponse
    {
        $response = $this->withSession([env('SESSION_USER_ID') => $userId])
            ->post("/devices/$action/$deviceId");

        return $response;
    }

    private function givenUserOwnsDeviceForDeletion(User $user): int
    {
        $deviceId = self::$faker->randomDigit();

        $mockDeviceModel = Mockery::mock(Device::class);
        $mockDeviceModel
            ->shouldReceive('find')->with($deviceId)->once()->andReturn(Mockery::self())
            ->shouldReceive('getAttribute')->with('name')->once()
            ->shouldReceive('destroy')->with($deviceId)->once();

        $this->app->instance(Device::class, $mockDeviceModel);

        $this->givenDoesUserOwnDevice($user, $deviceId, true);

        return $deviceId;
    }

    private function givenUserOwnsDeviceForUpdating(User $user, int $deviceId, string $originalDeviceName, string $originalDeviceDescription, int $originalOnCode, int $originalOffCode, int $originalPulseLength): Device
    {
        $rfDevice = new RFDevice();
        $rfDeviceProperties = $rfDevice->getFillable();

        $mockRfDevice = Mockery::mock(RFDevice::class);
        $mockRfDevice->shouldReceive('getFillable')->once()->andReturn($rfDeviceProperties);

        $mockDeviceModel = Mockery::mock(Device::class)->makePartial();
        $mockDeviceModel->name = $originalDeviceName;
        $mockDeviceModel->description = $originalDeviceDescription;
        $mockDeviceModel->on_code = $originalOnCode;
        $mockDeviceModel->off_code = $originalOffCode;
        $mockDeviceModel->pulse_length = $originalPulseLength;

        $mockDeviceModel
            ->shouldReceive('find')->with($deviceId)->once()->andReturnSelf()
            ->shouldReceive('getAttribute')->with('specificDevice')->atLeast()->once()->andReturnSelf()
            ->shouldReceive('first')->once()->andReturn($mockRfDevice)
            ->shouldReceive('save')->atLeast()->once();

        $this->app->instance(Device::class, $mockDeviceModel);

        $this->givenDoesUserOwnDevice($user, $deviceId, true);

        return $mockDeviceModel;
    }

    private function addDeviceForUser(Device $device, User $user): TestResponse
    {
        $mockDeviceModel = Mockery::mock(Device::class);
        $mockDeviceModel->shouldReceive('add')->withAnyArgs()->once()->andReturn($device);
        $this->app->instance(Device::class, $mockDeviceModel);

        $mockRFDeviceModel = Mockery::mock(RFDevice::class);
        $mockRFDeviceModel->shouldReceive('add')->withAnyArgs()->once();
        $this->app->instance(RFDevice::class, $mockRFDeviceModel);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->post('/devices/add', [
                'name' => $device->name,
                'description' => self::$faker->sentence(),
                'on_code' => self::$faker->randomDigit(),
                'off_code' => self::$faker->randomDigit(),
                'pulse_length' => self::$faker->randomDigit()
            ]);

        return $response;
    }

    private function givenSingleUserExistsWithDevicesContainingHtmlAttibutes(string $device1Name, string $device2Name, string $device3Name, string $attributeName, string $attributeValue): User
    {
        $userId = self::$faker->uuid();

        $user = $this->createUser($userId);

        $collection = new Device();

        $devices = $collection->newCollection(
            [
                $this->mockDeviceRecordWithHtmlAttributes($device1Name, $userId, $attributeName, $attributeValue),
                $this->mockDeviceRecordWithHtmlAttributes($device2Name, $userId, $attributeName, $attributeValue),
                $this->mockDeviceRecordWithHtmlAttributes($device3Name, $userId, $attributeName, $attributeValue)
            ]
        );

        $mockUserRecord = Mockery::mock(User::class)->makePartial();
        $mockUserRecord->shouldReceive('getAttribute')->with('devices')->andReturn($devices);

        $this->mockUserTable($mockUserRecord, $userId);

        return $user;
    }

    private function callUpdate(User $user, int $deviceId, string $newDeviceName, string $newDeviceDescription, int $newOnCode, int $newOffCode, int $newPulseLength): TestResponse
    {
        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->post('/devices/update/' . $deviceId, [
                'name' => $newDeviceName,
                'description' => $newDeviceDescription,
                'on_code' => $newOnCode,
                'off_code' => $newOffCode,
                'pulse_length' => $newPulseLength
            ]);

        return $response;
    }

    private function parametersForUpdatingDevice(): array
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();
        $newDeviceName = self::$faker->word();
        $newDeviceDescription = self::$faker->sentence();
        $newOnCode = self::$faker->randomNumber();
        $newOffCode = self::$faker->randomNumber();
        $newPulseLength = self::$faker->randomNumber();

        return [$user, $deviceId, $newDeviceName, $newDeviceDescription, $newOnCode, $newOffCode, $newPulseLength];
    }

    private function assertDevicePropertiesMatch(Device $device, string $originalDeviceName, string $originalDeviceDescription, int $originalOnCode, int $originalOffCode, int $originalPulseLength): void
    {
        $this->assertEquals($originalDeviceName, $device->name);
        $this->assertEquals($originalDeviceDescription, $device->description);
        $this->assertEquals($originalOnCode, $device->on_code);
        $this->assertEquals($originalOffCode, $device->off_code);
        $this->assertEquals($originalPulseLength, $device->pulse_length);
    }
}
