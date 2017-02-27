<?php

namespace Tests\Unit\Controller\Common;

use App\Device;
use App\User;
use Mockery;

class DeviceControllerTestCase extends ControllerTestCase
{
    protected function givenSingleUserExists()
    {
        $user = $this->createUser(self::$faker->uuid());

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $user->user_id)->andReturn(Mockery::self())
            ->shouldReceive('first')->andReturn($user);

        $this->app->instance(User::class, $mockUserTable);

        return $user;
    }

    protected function givenSingleUserExistsWithDevices($device1Name, $device2Name, $device3Name)
    {
        $userId = self::$faker->uuid();

        $user = $this->createUser($userId);

        $collection = new Device();

        $devices = $collection->newCollection(
            [
                $this->createDevice($device1Name, $userId),
                $this->createDevice($device2Name, $userId),
                $this->createDevice($device3Name, $userId)
            ]
        );

        $mockUserRecord = Mockery::mock(User::class)->makePartial();
        $mockUserRecord->shouldReceive('getAttribute')->with('devices')->once()->andReturn($devices);

        $this->mockUserTable($mockUserRecord, $userId);

        return $user;
    }

    protected function givenDeviceIsRegisteredToUser($device, $userId)
    {
        $collection = new Device();

        $deviceCollection = $collection->newCollection([$device]);

        $mockUserRecord = Mockery::mock(User::class)->makePartial();
        $mockUserRecord->shouldReceive('getAttribute')->with('devices')->once()->andReturn($deviceCollection);

        $this->mockUserTable($mockUserRecord, $userId);
    }

    protected function givenSingleUserExistsWithSingleDevice()
    {
        $userId = self::$faker->uuid();

        $collection = new Device();

        $device = $this->createDevice(self::$faker->name(), $userId);

        $devices = $collection->newCollection([$device]);

        $mockUserRecord = Mockery::mock(User::class)->makePartial();
        $mockUserRecord->shouldReceive('getAttribute')->with('devices')->once()->andReturn($devices);

        $this->mockUserTable($mockUserRecord, $userId);

        return $device;
    }

    protected function createUser($userId)
    {
        $user = new User();

        $user->id = self::$faker->randomDigit();
        $user->name = self::$faker->name();
        $user->email = self::$faker->email();
        $user->user_id = $userId;

        return $user;
    }

    protected function mockUserTable($mockUserRecord, $userId)
    {
        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $userId)->once()->andReturn(Mockery::self())
            ->shouldReceive('first')->once()->andReturn($mockUserRecord);

        $this->app->instance(User::class, $mockUserTable);
    }

    protected function createDevice($deviceName, $userId)
    {
        $device = new Device([
            'id' => self::$faker->randomDigit(),
            'name' => $deviceName,
            'description' => self::$faker->sentence(),
            'user_id' => $userId
        ]);

        return $device;
    }

    protected function givenDoesUserOwnDevice($user, $deviceId, $doesUserOwnDevice)
    {
        $mockUserRecord = Mockery::mock(User::class);
        $mockUserRecord->shouldReceive('doesUserOwnDevice')->with($deviceId)->once()->andReturn($doesUserOwnDevice);

        $this->mockUserTable($mockUserRecord, $user->user_id);
    }
}
