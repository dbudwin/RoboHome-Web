<?php

namespace Tests\Unit\Repositories;

use App\Repositories\IRFDeviceRepository;
use App\RFDevice;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RFDeviceRepositoryTest extends RepositoryTestCaseWithRealDatabase
{
    private $rfDeviceRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->rfDeviceRepository = $this->app->make(IRFDeviceRepository::class);
    }

    public function testCreate_GivenRFDeviceExists_ReturnsCreatedDevice(): void
    {
        $this->assertEquals(0, RFDevice::count());

        $this->createRFDevice();

        $this->assertEquals(1, RFDevice::count());
    }

    public function testGet_GivenRFDeviceDoesNotExist_ThrowsModelNotFoundException(): void
    {
        $nonexistentRfDeviceId = self::$faker->randomNumber();

        $this->expectException(ModelNotFoundException::class);

        $this->rfDeviceRepository->get($nonexistentRfDeviceId);
    }

    public function testGet_GivenRFDeviceExists_ReturnsRFDevice(): void
    {
        $rfDevice = $this->createRFDevice();

        $retrievedRfDevice = $this->rfDeviceRepository->get($rfDevice->device_id);

        $this->assertTrue($retrievedRfDevice->is($rfDevice));
    }

    public function testUpdate_GivenRFDeviceDoesNotExist_ThrowsModelNotFoundException(): void
    {
        $nonexistentRfDeviceId = self::$faker->randomNumber();
        $properties = $this->createDeviceProperties();

        $this->expectException(ModelNotFoundException::class);

        $this->rfDeviceRepository->update($nonexistentRfDeviceId, $properties);
    }

    public function testUpdate_GivenRFDeviceExists_RFDevicePropertiesUpdated(): void
    {
        $originalRfDevice = $this->createRFDevice();

        $newProperties = $this->createDeviceProperties();

        $updatedRfDevice = $this->rfDeviceRepository->update($originalRfDevice->device_id, $newProperties);

        $this->assertNotEquals($originalRfDevice->on_code, $updatedRfDevice->on_code);
        $this->assertNotEquals($originalRfDevice->off_code, $updatedRfDevice->off_code);
        $this->assertNotEquals($originalRfDevice->pulse_length, $updatedRfDevice->pulse_length);
    }

    private function createRFDevice(): RFDevice
    {
        $deviceId = self::$faker->randomNumber();
        $properties = $this->createDeviceProperties();

        $rfDevice = $this->rfDeviceRepository->create($deviceId, $properties);

        return $rfDevice;
    }
}
