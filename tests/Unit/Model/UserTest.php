<?php

namespace Tests\Unit\Model;

use App\Device;
use App\User;

class UserTest extends TestCaseWithRealDatabase
{
    public function testDevices_GivenNoDevicesExist_ReturnsZeroDevices(): void
    {
        $user = $this->createUser();

        $devices = $user->devices();

        $this->assertEquals(0, $devices->count());
    }

    public function testDevices_GivenUserHasSeveralDevices_ReturnsAllDevices(): void
    {
        $user = $this->createUser();
        $numberOfDevices = self::$faker->randomDigit();

        $this->createSeveralDevicesForUser($user, $numberOfDevices);

        $devices = $user->devices();

        $this->assertEquals($numberOfDevices, $devices->count());
    }

    public function testOwnsDevice_GivenFirstUserDoesNotOwnAnyDevices_ReturnsFalse(): void
    {
        $firstUser = $this->createUser();
        $secondUser = $this->createUser();

        $deviceIdForSecondUser = $this->createSingleDeviceForUser($secondUser)->id;

        $userOwnsDevice = $firstUser->ownsDevice($deviceIdForSecondUser);

        $this->assertFalse($userOwnsDevice);
    }

    public function testOwnsDevice_GivenUserOwnsDevice_ReturnsTrue(): void
    {
        $user = $this->createUser();
        $device = $this->createSingleDeviceForUser($user);

        $userOwnsDevice = $user->ownsDevice($device->id);

        $this->assertTrue($userOwnsDevice);
    }

    private function createUser(): User
    {
        $user = factory(User::class)->create();

        return $user;
    }

    private function createSingleDeviceForUser(User $user): Device
    {
        $device = factory(Device::class)->create([
            'user_id' => $user->id
        ]);

        return $device;
    }

    private function createSeveralDevicesForUser(User $user, int $numberOfDevicesForUser): void
    {
        factory(Device::class, $numberOfDevicesForUser)->create([
            'user_id' => $user->id
        ]);
    }
}
