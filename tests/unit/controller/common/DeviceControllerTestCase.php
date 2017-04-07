<?php

namespace Tests\Unit\Controller\Common;

use App\Device;
use App\Http\MQTT\MessagePublisher;
use App\User;
use Mockery;
use Mockery\MockInterface;

class DeviceControllerTestCase extends ControllerTestCase
{
    protected function givenSingleUserExists() : User
    {
        $user = $this->createUser(self::$faker->uuid());

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $user->user_id)->andReturn(Mockery::self())
            ->shouldReceive('first')->andReturn($user);

        $this->app->instance(User::class, $mockUserTable);

        return $user;
    }

    protected function givenSingleUserExistsWithDevices(string $device1Name, string $device2Name, string $device3Name) : User
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

    protected function givenDeviceIsRegisteredToUser(Device $device, string $userId)
    {
        $collection = new Device();

        $deviceCollection = $collection->newCollection([$device]);

        $mockUserRecord = Mockery::mock(User::class)->makePartial();
        $mockUserRecord->shouldReceive('getAttribute')->with('devices')->once()->andReturn($deviceCollection);

        $this->mockUserTable($mockUserRecord, $userId);
    }

    protected function givenSingleUserExistsWithSingleDevice() : Device
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

    protected function createUser(string $userId) : User
    {
        $user = new User();

        $user->id = self::$faker->randomDigit();
        $user->name = self::$faker->name();
        $user->email = self::$faker->email();
        $user->user_id = $userId;

        return $user;
    }

    protected function mockUserTable(MockInterface $mockUserRecord, string $userId)
    {
        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('user_id', $userId)->once()->andReturn(Mockery::self())
            ->shouldReceive('first')->once()->andReturn($mockUserRecord);

        $this->app->instance(User::class, $mockUserTable);
    }

    protected function mockMessagePublisher(int $timesPublishIsCalled = 1)
    {
        $mockMessagePublisher = Mockery::mock(MessagePublisher::class);
        $mockMessagePublisher
            ->shouldReceive('publish')
            ->withAnyArgs()->times($timesPublishIsCalled)
            ->andReturn(true);

        $this->app->instance(MessagePublisher::class, $mockMessagePublisher);
    }

    protected function createDevice(string $deviceName, string $userId) : Device
    {
        Device::unguard();

        $device = new Device([
            'id' => self::$faker->randomDigit(),
            'name' => $deviceName,
            'description' => self::$faker->sentence(),
            'user_id' => $userId
        ]);

        Device::reguard();

        return $device;
    }

    protected function givenDoesUserOwnDevice(User $user, int $deviceId, bool $doesUserOwnDevice) : MockInterface
    {
        $mockUserRecord = Mockery::mock(User::class);
        $mockUserRecord->shouldReceive('doesUserOwnDevice')->with($deviceId)->once()->andReturn($doesUserOwnDevice);

        $this->mockUserTable($mockUserRecord, $user->user_id);

        return $mockUserRecord;
    }
}
