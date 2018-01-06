<?php

namespace Tests\Unit\Repositories;

use App\Device;
use App\Repositories\IDeviceRepository;
use App\Repositories\IRFDeviceRepository;
use App\RFDevice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;

class DeviceRepositoryTest extends RepositoryTestCaseWithRealDatabase
{
    private $deviceRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->deviceRepository = $this->app->make(IDeviceRepository::class);
    }

    public function testCreate_GivenUserExists_ReturnsCreatedDevice(): void
    {
        $user = $this->createUser();
        $properties = $this->createDeviceProperties();

        $this->assertEquals(0, Device::count());
        $this->assertEquals(0, RFDevice::count());

        $this->deviceRepository->create($properties, $user->id);

        $this->assertEquals(1, Device::count());
        $this->assertEquals(1, RFDevice::count());
    }

    public function testGet_GivenDeviceDoesNotExist_ThrowsModelNotFoundException(): void
    {
        $nonexistentDeviceId = self::$faker->randomNumber();

        $this->expectException(ModelNotFoundException::class);

        $this->deviceRepository->get($nonexistentDeviceId);
    }

    public function testGet_GivenDeviceExists_ReturnsRFDevice(): void
    {
        $device = $this->createDevice();

        $retrievedDevice = $this->deviceRepository->get($device->id);

        $this->assertTrue($retrievedDevice->is($device));
    }

    public function testUpdate_GivenDeviceDoesNotExist_ThrowsModelNotFoundException(): void
    {
        $nonexistentDeviceId = self::$faker->randomNumber();
        $properties = $this->createDeviceProperties();

        $this->expectException(ModelNotFoundException::class);

        $this->deviceRepository->update($nonexistentDeviceId, $properties);
    }

    public function testUpdate_GivenDeviceExists_DevicePropertiesUpdated(): void
    {
        $originalDevice = $this->createDevice();
        $newProperties = $this->createDeviceProperties();

        $updatedDevice = $this->deviceRepository->update($originalDevice->user_id, $newProperties);

        $this->assertNotEqualOrEmpty($originalDevice->name, $updatedDevice->name);
        $this->assertNotEqualOrEmpty($originalDevice->description, $updatedDevice->description);
    }

    public function testUpdate_GivenDeviceExists_CallsUpdateForSpecificDevice(): void
    {
        $mockRfDeviceRepository = Mockery::mock(IRFDeviceRepository::class)->makePartial();
        $mockRfDeviceRepository->shouldReceive('update')->once();

        $this->app->instance(IRFDeviceRepository::class, $mockRfDeviceRepository);

        $this->deviceRepository = $this->app->make(IDeviceRepository::class);

        $originalDevice = $this->createDevice();
        $newProperties = $this->createDeviceProperties();

        $this->deviceRepository->update($originalDevice->id, $newProperties);
    }

    public function testDelete_GivenDeviceDoesNotExist_ReturnsFalse(): void
    {
        $nonexistentDeviceId = self::$faker->randomNumber();

        $deleted = $this->deviceRepository->delete($nonexistentDeviceId);

        $this->assertFalse($deleted);
    }

    public function testDelete_GivenDeviceExists_ReturnsTrue(): void
    {
        $device = $this->createDevice();

        $deleted = $this->deviceRepository->delete($device->id);

        $this->assertTrue($deleted);
    }

    public function testName_GivenDeviceDoesNotExist_ThrowsModelNotFoundException(): void
    {
        $nonexistentDeviceId = self::$faker->randomNumber();

        $this->expectException(ModelNotFoundException::class);

        $this->deviceRepository->name($nonexistentDeviceId);
    }

    public function testName_GivenDeviceExists_ReturnsName(): void
    {
        $device = $this->createDevice();

        $name = $this->deviceRepository->name($device->id);

        $this->assertEquals($device->name, $name);
    }

    private function createDevice(): Device
    {
        $device = factory(Device::class)->create([
            'user_id' => $this->createUser()->id
        ]);

        factory(RFDevice::class)->create([
            'device_id' => $device->id
        ]);

        return $device;
    }
}
