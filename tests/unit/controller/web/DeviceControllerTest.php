<?php

namespace Tests\Unit\Controller\Web;

use App\Device;
use App\RFDevice;
use App\User;
use Mockery;
use Tests\Unit\Controller\Common\ControllerTestCase;

class DeviceControllerTest extends ControllerTestCase
{
    public function testDevices_GivenUserNotLoggedIn_RedirectToIndex()
    {
        $response = $this->get('/devices');

        $this->assertRedirectedToRouteWith302($response, '/');
        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }

    public function testDevices_GivenUserLoggedIn_ViewContainsUsersName()
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

    public function testDevices_GivenUserLoggedIn_ViewContainsUsersDevices()
    {
        $device1Name = self::$faker->word();
        $device2Name = self::$faker->word();
        $device3Name = self::$faker->word();

        $user = $this->givenSingleUserExistsWithDevices($device1Name, $device2Name, $device3Name);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])->get('/devices');

        $response->assertSee($device1Name);
        $response->assertSee($device2Name);
        $response->assertSee($device3Name);
        $response->assertStatus(200);
    }

    public function testAdd_GivenPostedData_RedirectToDevices()
    {
        $user = $this->givenSingleUserExists();
        $deviceName = self::$faker->word();

        $response = $this->addDeviceForUser($user->user_id, $deviceName);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testAdd_CallsAddForModels()
    {
        $user = $this->givenSingleUserExists();
        $deviceName = self::$faker->name();

        $this->addDeviceForUser($user->user_id, $deviceName);
    }

    public function testDelete_GivenDeviceIdThatUserDoesntOwn_RedirectToDevices()
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();

        $this->givenDoesUserOwnDevice($user, $deviceId, false);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->get('/devices/delete/' . $deviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testDelete_GivenDeviceIdThatUserOwns_RedirectToDevices()
    {
        $user = $this->givenSingleUserExists();
        $deviceId = self::$faker->randomDigit();

        $mockDeviceModel = Mockery::mock(Device::class);
        $mockDeviceModel->shouldReceive('destroy')->with($deviceId)->once();
        $this->app->instance(Device::class, $mockDeviceModel);

        $this->givenDoesUserOwnDevice($user, $deviceId, true);

        $response = $this->withSession([env('SESSION_USER_ID') => $user->user_id])
            ->get('/devices/delete/' . $deviceId);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    private function givenSingleUserExists()
    {
        $user = $this->createUser(self::$faker->uuid());

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $user->user_id)->andReturn(Mockery::self())
            ->shouldReceive('first')->andReturn($user);

        $this->app->instance(User::class, $mockUserTable);

        return $user;
    }

    private function givenSingleUserExistsWithDevices($device1Name, $device2Name, $device3Name)
    {
        $userId = self::$faker->uuid();

        $user = $this->createUser($userId);

        $collection = new Device();

        $devices = $collection->newCollection(
            [
                new Device(['name' => $device1Name, 'user_id' => $userId]),
                new Device(['name' => $device2Name, 'user_id' => $userId]),
                new Device(['name' => $device3Name, 'user_id' => $userId])
            ]
        );

        $mockUserRecord = Mockery::mock(User::class)->makePartial();
        $mockUserRecord->shouldReceive('getAttribute')->with('devices')->once()->andReturn($devices);

        $this->mockUserTable($mockUserRecord, $userId);

        return $user;
    }

    private function addDeviceForUser($userId, $deviceName)
    {
        $mockDeviceModel = Mockery::mock(Device::class);
        $mockDeviceModel->shouldReceive('add')->withAnyArgs()->once()->andReturn(new Device());
        $this->app->instance(Device::class, $mockDeviceModel);

        $mockRFDeviceModel = Mockery::mock(RFDevice::class);
        $mockRFDeviceModel->shouldReceive('add')->withAnyArgs()->once();
        $this->app->instance(RFDevice::class, $mockRFDeviceModel);

        $response = $this->withSession([env('SESSION_USER_ID') => $userId])
            ->call('POST', '/devices/add', [
                'name' => $deviceName,
                'description' => self::$faker->sentence(),
                'onCode' => self::$faker->randomDigit(),
                'offCode' => self::$faker->randomDigit(),
                'pulseLength' => self::$faker->randomDigit()
            ]);

        return $response;
    }

    private function createUser($userId)
    {
        $user = new User();

        $user->id = self::$faker->randomDigit();
        $user->name = self::$faker->name();
        $user->email = self::$faker->email();
        $user->user_id = $userId;

        return $user;
    }

    private function givenDoesUserOwnDevice($user, $deviceId, $doesUserOwnDevice)
    {
        $mockUserRecord = Mockery::mock(User::class);
        $mockUserRecord->shouldReceive('doesUserOwnDevice')->with($deviceId)->once()->andReturn($doesUserOwnDevice);

        $this->mockUserTable($mockUserRecord, $user->user_id);
    }

    private function mockUserTable($mockUserRecord, $userId)
    {
        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $userId)->once()->andReturn(Mockery::self())
            ->shouldReceive('first')->once()->andReturn($mockUserRecord);

        $this->app->instance(User::class, $mockUserTable);
    }
}
