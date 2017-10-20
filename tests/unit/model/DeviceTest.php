<?php

namespace Tests\Unit\Model;

use App\Device;
use App\Http\Globals\DeviceTypes;
use App\RFDevice;
use Illuminate\Database\Eloquent\Model;

class DeviceTest extends ModelTestCase
{
    public function testAdd_GivenDeviceAddedToDatabase_DatabaseOnlyHasOneDeviceRecord(): void
    {
        $device = new Device();
        $name = self::$faker->word();
        $description = self::$faker->sentence();
        $userId = self::$faker->randomNumber();
        $type = self::$faker->randomNumber();

        $addedDevice = $device->add($name, $description, $userId, $type);

        $this->assertCount(1, Device::all());
        $this->assertEquals($name, $addedDevice->name);
        $this->assertEquals($description, $addedDevice->description);
        $this->assertEquals($type, $addedDevice->device_type_id);
        $this->assertEquals($userId, $addedDevice->user_id);
    }

    public function testHtmlDataAttributesForSpecificDeviceProperties_GivenDeviceAddedToDatabase_AttributesMatchNameAndValueOfAddedDevice(): void
    {
        foreach ($this->deviceTypeConstants() as $specificDeviceType) {
            $addedDevice = $this->addDeviceToDatabase($specificDeviceType);
            $specificDevice = $this->addSpecificDevice($addedDevice, $specificDeviceType);
            $specificDeviceProperties = $addedDevice->specificDevice()->first()->getFillable();
            $htmlAttributes = $addedDevice->htmlDataAttributesForSpecificDeviceProperties();

            $attributeNames = [];
            $attributeValues = [];

            foreach ($htmlAttributes as $htmlAttribute) {
                $htmlAttributePieces = explode('=', $htmlAttribute);
                $htmlAttributeRawNameLength = strlen($htmlAttributePieces[0]);
                $htmlAttributeDataPrefix = 'data-device-';
                $attributeName = substr($htmlAttributePieces[0], strlen($htmlAttributeDataPrefix), $htmlAttributeRawNameLength);
                $attributeName = str_replace('-', '_', $attributeName);

                array_push($attributeNames, $attributeName);
                array_push($attributeValues, $htmlAttributePieces[1]);
            }

            $this->assertHtmlAttributesMatchSpecificDeviceProperties($specificDevice, $attributeNames, $attributeValues, $specificDeviceProperties);
        }
    }

    public function testSpecificDevice_GivenDeviceAddedToDatabase_SpecificDeviceMatches(): void
    {
        foreach ($this->deviceTypeConstants() as $specificDeviceType) {
            $addedDevice = $this->addDeviceToDatabase($specificDeviceType);
            $specificDevice = $this->addSpecificDevice($addedDevice, $specificDeviceType);
            $foundSpecificDevice = $addedDevice->specificDevice;
            $properties = $foundSpecificDevice->getFillable();

            foreach ($properties as $property) {
                $this->assertEquals($specificDevice->$property, $foundSpecificDevice->$property);
            }

            $this->assertEquals(RFDevice::class, get_class($foundSpecificDevice));
        }
    }

    public function testSpecificDevice_GivenDeviceAddedToDatabase_CascadeDeleteRemovesDeviceAndSpecificDevice(): void
    {
        foreach ($this->deviceTypeConstants() as $specificDeviceType) {
            $addedDevice = $this->addDeviceToDatabase($specificDeviceType);
            $specificDevice = $this->addSpecificDevice($addedDevice, $specificDeviceType);

            $this->assertEquals(1, Device::count());
            $this->assertEquals(1, $specificDevice::count());

            $addedDevice->delete();

            $this->assertEquals(0, Device::count());
            $this->assertEquals(0, $specificDevice::count());
        }
    }

    private function addDeviceToDatabase(int $deviceType): Device
    {
        $device = new Device();
        $name = self::$faker->word();
        $description = self::$faker->sentence();
        $type = $deviceType;
        $userId = self::$faker->randomNumber();

        $addedDevice = $device->add($name, $description, $userId, $type);

        return $addedDevice;
    }

    private function addRFDeviceToDatabase(Device $device): RFDevice
    {
        $rfDevice = new RFDevice();
        $onCode = self::$faker->randomNumber();
        $offCode = self::$faker->randomNumber();
        $pulseLength = self::$faker->randomNumber();
        $deviceId = $device->id;

        $rfDevice = $rfDevice->add($onCode, $offCode, $pulseLength, $deviceId);

        return $rfDevice;
    }

    private function assertHtmlAttributesMatchSpecificDeviceProperties(Model $specificDevice, array $attributeNames, array $attributeValues, array $specificDeviceProperties): void
    {
        $this->assertEquals(sizeof($attributeNames), sizeof($specificDeviceProperties));

        for ($i = 0; $i < sizeof($attributeNames); $i++) {
            $this->assertEquals($specificDeviceProperties[$i], $attributeNames[$i]);
            $this->assertEquals($specificDevice->{$attributeNames[$i]}, $attributeValues[$i]);
        }
    }

    private function addSpecificDevice(Model $addedDevice, int $specificDeviceType): RFDevice
    {
        if ($specificDeviceType == DeviceTypes::RF_DEVICE) {
            $specificDevice = $this->addRFDeviceToDatabase($addedDevice);
        }

        return $specificDevice;
    }

    private function deviceTypeConstants(): array
    {
        $deviceTypesClass = new \ReflectionClass(DeviceTypes::class);
        $specificDeviceTypes = $deviceTypesClass->getConstants();

        return $specificDeviceTypes;
    }
}
