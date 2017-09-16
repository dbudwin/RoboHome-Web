<?php

namespace Tests\Unit\Controller\Common;

use App\Device;
use App\Http\MQTT\MessagePublisher;
use App\User;
use Mockery;
use Mockery\MockInterface;

class DevicesControllerTestCase extends ControllerTestCase
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
                $this->mockDeviceRecord($device1Name, $userId),
                $this->mockDeviceRecord($device2Name, $userId),
                $this->mockDeviceRecord($device3Name, $userId)
            ]
        );

        $mockUserRecord = Mockery::mock(User::class)->makePartial();
        $mockUserRecord->shouldReceive('getAttribute')->with('devices')->andReturn($devices);

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

    protected function mockDeviceRecord(string $deviceName, string $userId) : Device
    {
        $mockDeviceRecord = $this->createMockDeviceRecord($deviceName, $userId);

        $this->app->instance(Device::class, $mockDeviceRecord);

        return $mockDeviceRecord;
    }

    protected function mockDeviceRecordWithHtmlAttributes(string $deviceName, string $userId, string $attributeName, string $attributeValue) : Device
    {
        $mockDeviceRecord = $this->createMockDeviceRecord($deviceName, $userId);

        $htmlAttribute = 'data-device-' . $attributeName . '=' . $attributeValue;

        $mockDeviceRecord->shouldReceive('htmlDataAttributesForSpecificDeviceProperties')->andReturn([
            $htmlAttribute
        ]);

        $this->app->instance(Device::class, $mockDeviceRecord);

        return $mockDeviceRecord;
    }

    protected function givenDoesUserOwnDevice(User $user, int $deviceId, bool $doesUserOwnDevice) : MockInterface
    {
        $mockUserRecord = Mockery::mock(User::class);
        $mockUserRecord->shouldReceive('doesUserOwnDevice')->with($deviceId)->once()->andReturn($doesUserOwnDevice);

        $this->mockUserTable($mockUserRecord, $user->user_id);

        return $mockUserRecord;
    }

    private function createMockDeviceRecord(string $deviceName, string $userId): MockInterface
    {
        $deviceType = self::$faker->randomDigit();

        $mockDeviceRecord = Mockery::mock(Device::class)->makePartial();
        $mockDeviceRecord->shouldReceive('getAttribute')->with('id')->andReturn(self::$faker->randomDigit())
            ->shouldReceive('getAttribute')->with('name')->andReturn($deviceName)
            ->shouldReceive('getAttribute')->with('description')->andReturn(self::$faker->sentence())
            ->shouldReceive('getAttribute')->with('user_id')->andReturn($userId)
            ->shouldReceive('getAttribute')->with('device_type_id')->andReturn($deviceType);

        return $mockDeviceRecord;
    }
}
