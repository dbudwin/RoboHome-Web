<?php

namespace Tests\Unit\Controller\Web;

use App\Device;
use App\Repositories\IDeviceRepository;
use App\User;
use Illuminate\Foundation\Testing\TestResponse;
use Mockery;
use Tests\Unit\Controller\Common\DevicesControllerTestCase;
use Webpatser\Uuid\Uuid;

class DevicesControllerTest extends DevicesControllerTestCase
{
    public function testDevices_GivenUserNotLoggedIn_RedirectToLogin(): void
    {
        $response = $this->get('/devices');

        $this->assertRedirectedToRouteWith302($response, '/login');
    }

    public function testDevices_GivenUserLoggedIn_ViewContainsUsersName(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/devices');

        $response->assertSee($user->name . '\'s Controllable Devices');
        $response->assertSuccessful();
    }

    public function testDevices_GivenUserLoggedIn_ViewContainsUsersDevices(): void
    {
        $user = $this->makeUser();
        $deviceName = self::$faker->word();
        $deviceDescription = self::$faker->sentence();
        $htmlAttributeName = self::$faker->word();
        $htmlAttributeValue = self::$faker->randomDigit();
        $htmlAttribute = 'data-device-' . $htmlAttributeName . '=' . $htmlAttributeValue;

        $mockDevice = Mockery::mock(Device::class);
        $mockDevice
            ->shouldReceive('getAttribute')->with('public_id')->atLeast()->once()->andReturn(self::$faker->randomDigit())
            ->shouldReceive('getAttribute')->with('name')->atLeast()->once()->andReturn($deviceName)
            ->shouldReceive('getAttribute')->with('description')->atLeast()->once()->andReturn($deviceDescription)
            ->shouldReceive('htmlDataAttributesForSpecificDevice')->once()->andReturn([ $htmlAttribute ]);

        $this->app->instance(Device::class, $mockDevice);

        $mockUser = Mockery::mock(User::class);
        $mockUser
            ->shouldReceive('hasVerifiedEmail')->once()->andReturn(true)
            ->shouldReceive('getAttribute')->with('name')->once()->andReturn($user->name)
            ->shouldReceive('getAttribute')->with('devices')->once()->andReturn([$mockDevice]);

        $response = $this->actingAs($mockUser)->get('/devices');

        $response->assertSee($htmlAttribute);
        $response->assertSee($deviceName);
        $response->assertSee($deviceDescription);
        $response->assertSuccessful();
    }

    public function testAdd_GivenPostedData_CallsAddOnModelsThenRedirectsToDevices(): void
    {
        $user = $this->makeUser();
        $device = $this->makeDevice();

        $response = $this->callAdd($device, $user);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testAdd_GivenSingleUserExists_SessionContainsSuccessMessage(): void
    {
        $user = $this->makeUser();
        $device = $this->makeDevice();

        $response = $this->callAdd($device, $user);

        $response->assertSessionHas('alert-success');
    }

    public function testDelete_GivenUserDoesNotOwnDevice_RedirectToDevices(): void
    {
        $response = $this->callDeleteOnDeviceUserDoesNotOwn();

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testDelete_GivenUserDoesNotOwnDevice_SessionContainsErrorMessage(): void
    {
        $response = $this->callDeleteOnDeviceUserDoesNotOwn();

        $response->assertSessionHas('alert-danger');
    }

    public function testDelete_GivenUserOwnsDevice_RedirectToDevices(): void
    {
        $response = $this->callDeleteOnDeviceUserOwns();

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testDelete_GivenUserOwnsDevice_SessionContainsSuccessMessage(): void
    {
        $response = $this->callDeleteOnDeviceUserOwns();

        $response->assertSessionHas('alert-success');
    }

    public function testDelete_GivenUserOwnsDeviceThatHasAlreadyBeenDeleted_SessionContainsErrorMessage(): void
    {
        $response = $this->callDeleteOnDeviceUserOwns(false);

        $response->assertSessionHas('alert-danger');
    }

    public function testDelete_GivenInvalidUrlParameter_Returns404(): void
    {
        $user = $this->makeUser();
        $invalidUrlParameter = (string)self::$faker->randomNumber();

        $response = $this->actingAs($user)->get("/devices/delete/$invalidUrlParameter");

        $response->assertNotFound();
    }

    public function testUpdate_GivenUserDoesNotOwnDevice_RedirectToDevices(): void
    {
        $device = $this->makeDevice();

        $mockUser = $this->mockUserOwnsDevice($device->id, false);

        $response = $this->callUpdateOnDeviceUserDoesNotOwn($mockUser, $device);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testUpdate_GivenUserDoesNotOwnDevice_SessionContainsErrorMessage(): void
    {
        $device = $this->makeDevice();

        $mockUser = $this->mockUserOwnsDevice($device->id, false);

        $response = $this->callUpdateOnDeviceUserDoesNotOwn($mockUser, $device);

        $response->assertSessionHas('alert-danger');
    }

    public function testUpdate_GivenUserOwnsDevice_RedirectToDevices(): void
    {
        $device = $this->makeDevice();

        $mockUser = $this->mockUserOwnsDevice($device->id, true);

        $response = $this->callUpdateOnDeviceUserOwns($mockUser, $device);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testUpdate_GivenUserOwnsDevice_SessionContainsSuccessMessage(): void
    {
        $device = $this->makeDevice();

        $mockUser = $this->mockUserOwnsDevice($device->id, true);

        $response = $this->callUpdateOnDeviceUserOwns($mockUser, $device);

        $response->assertSessionHas('alert-success');
    }

    public function testUpdate_GivenInvalidUrlParameter_Returns404(): void
    {
        $user = $this->makeUser();
        $invalidUrlParameter = (string)self::$faker->randomNumber();

        $response = $this->putToUpdateWithUserAndDeviceId($user, $invalidUrlParameter);

        $response->assertNotFound();
    }

    public function testHandleControlRequest_GivenUserExistsWithNoDevices_PublishIsNotCalled(): void
    {
        $device = $this->makeDevice();
        $publicDeviceId = $device->public_id;
        $action = self::$faker->word();

        $mockUser = $this->mockUserOwnsDevice($device->id, false);
        $mockUser->shouldReceive('getAttribute')->with('id')->never();

        $this->mockMessagePublisher(0);
        $this->givenGetForPublicIdCalledForDevice($device);

        $response = $this->callControl($mockUser, $action, $publicDeviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testHandleControlRequest_GivenUserExistsWithNoDevices_SessionContainsErrorMessage(): void
    {
        $device = $this->makeDevice();
        $publicDeviceId = $device->public_id;
        $action = self::$faker->word();

        $mockUser = $this->mockUserOwnsDevice($device->id, false);

        $this->givenGetForPublicIdCalledForDevice($device);

        $response = $this->callControl($mockUser, $action, $publicDeviceId);

        $response->assertSessionHas('alert-danger');
    }

    public function testHandleControlRequest_GivenUserExistsWithDevice_CallsPublish(): void
    {
        $device = $this->makeDevice();
        $publicUserId = $device->public_id;
        $action = self::$faker->word();

        $mockUser = $this->mockUserOwnsDevice($device->id, true);
        $mockUser->shouldReceive('getAttribute')->with('public_id')->once()->andReturn($publicUserId);

        $this->givenGetForPublicIdCalledForDevice($device);

        $this->mockMessagePublisher(1);

        $response = $this->callControl($mockUser, $action, $publicUserId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testHandleControlRequest_GivenInvalidUrlParameter_Returns404(): void
    {
        $user = $this->makeUser();
        $invalidUrlParameter = (string)self::$faker->randomNumber();
        $action = self::$faker->word();

        $response = $this->callControl($user, $action, $invalidUrlParameter);

        $response->assertNotFound();
    }

    private function callAdd(Device $device, User $user): TestResponse
    {
        $this->givenActionCalledOnDeviceRepository($device, 'create');

        $csrfToken = self::$faker->uuid();

        return $this->actingAs($user)->withSession(['_token' => $csrfToken])
            ->post('/devices/add', [
                'name' => $device->name,
                'description' => self::$faker->sentence(),
                'on_code' => self::$faker->randomDigit(),
                'off_code' => self::$faker->randomDigit(),
                'pulse_length' => self::$faker->randomDigit(),
                '_token' => $csrfToken
            ]);
    }

    private function callDeleteOnDeviceUserDoesNotOwn(): TestResponse
    {
        $device = $this->makeDevice();
        $deviceId = $device->id;
        $publicDeviceId = $device->public_id;

        $mockUser = $this->mockUserOwnsDevice($deviceId, false);

        $mockDeviceRepository = Mockery::mock(IDeviceRepository::class);
        $mockDeviceRepository
            ->shouldReceive('name')->never()->with($deviceId)
            ->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($publicDeviceId) {
                return $argument instanceof Uuid && $argument == Uuid::import($publicDeviceId);
            }))->once()->andReturn($device)
            ->shouldReceive('delete')->never()->with($deviceId);

        $this->app->instance(IDeviceRepository::class, $mockDeviceRepository);

        return $this->actingAs($mockUser)->get("/devices/delete/$publicDeviceId");
    }

    private function callDeleteOnDeviceUserOwns(bool $wasDeleteSuccessful = true): TestResponse
    {
        $device = $this->makeDevice();
        $deviceId = $device->id;
        $publicDeviceId = $device->public_id;

        $mockUser = $this->mockUserOwnsDevice($deviceId, true);

        $mockDeviceRepository = Mockery::mock(IDeviceRepository::class);
        $mockDeviceRepository
            ->shouldReceive('name')->once()->with($deviceId)
            ->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($publicDeviceId) {
                return $argument instanceof Uuid && $argument == Uuid::import($publicDeviceId);
            }))->once()->andReturn($device)
            ->shouldReceive('delete')->once()->with($deviceId)->andReturn($wasDeleteSuccessful);

        $this->app->instance(IDeviceRepository::class, $mockDeviceRepository);

        $response = $this->actingAs($mockUser)->get("/devices/delete/$publicDeviceId");

        return $response;
    }

    private function callUpdateOnDeviceUserDoesNotOwn(User $user, Device $device): TestResponse
    {
        $mockDeviceRepository = Mockery::mock(IDeviceRepository::class);
        $mockDeviceRepository
            ->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
                return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
            }))->once()->andReturn($device)
            ->shouldReceive('update')->never();

        $this->app->instance(IDeviceRepository::class, $mockDeviceRepository);

        return $this->putToUpdateWithUserAndDeviceId($user, $device->public_id);
    }

    private function callUpdateOnDeviceUserOwns(User $user, Device $device): TestResponse
    {
        $mockDeviceRepository = $this->givenActionCalledOnDeviceRepository($device, 'update');
        $mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
            return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
        }))->once()->andReturn($device);

        return $this->putToUpdateWithUserAndDeviceId($user, $device->public_id);
    }

    private function putToUpdateWithUserAndDeviceId(User $user, string $publicDeviceId): TestResponse
    {
        $csrfToken = self::$faker->uuid();

        return $this->actingAs($user)->withSession(['_token' => $csrfToken])
            ->put("/devices/update/$publicDeviceId", [
                'name' => self::$faker->word(),
                'description' => self::$faker->sentence(),
                'on_code' => self::$faker->randomNumber(),
                'off_code' => self::$faker->randomNumber(),
                'pulse_length' => self::$faker->randomNumber(),
                '_token' => $csrfToken
            ]);
    }

    private function callControl(User $user, string $action, string $publicDeviceId): TestResponse
    {
        $csrfToken = self::$faker->uuid();

        return $this->actingAs($user)->withSession(['_token' => $csrfToken])
            ->post("/devices/$action/$publicDeviceId", [
                '_token' => $csrfToken
            ]);
    }

    protected function makeUser(): User
    {
        return factory(User::class)->make([
            'id' => self::$faker->randomNumber()
        ]);
    }

    private function makeDevice(): Device
    {
        return factory(Device::class)->make([
            'id' => self::$faker->randomNumber()
        ]);
    }

    private function mockUserOwnsDevice(int $deviceId, bool $userOwnsDevice): User
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser
            ->shouldReceive('ownsDevice')->with($deviceId)->once()->andReturn($userOwnsDevice)
            ->shouldReceive('hasVerifiedEmail')->once()->andReturn(true);

        return $mockUser;
    }

    private function givenActionCalledOnDeviceRepository(Device $device, string $action): IDeviceRepository
    {
        $mockDeviceRepository = Mockery::mock(IDeviceRepository::class);
        $mockDeviceRepository->shouldReceive($action)->once()->andReturn($device);

        return $this->app->instance(IDeviceRepository::class, $mockDeviceRepository);
    }

    private function givenGetForPublicIdCalledForDevice(Device $device): void
    {
        $mockDeviceRepository = Mockery::mock(IDeviceRepository::class);
        $mockDeviceRepository->shouldReceive('getForPublicId')->with(Mockery::on(function (Uuid $argument) use ($device) {
            return $argument instanceof Uuid && $argument == Uuid::import($device->public_id);
        }))->once()->andReturn($device);

        $this->app->instance(IDeviceRepository::class, $mockDeviceRepository);
    }
}
