<?php

namespace Tests\Unit\Model;

use App\Device;
use App\Http\Globals\DeviceTypes;
use App\RFDevice;
use Illuminate\Database\Eloquent\Model;

class DeviceTest extends TestCaseWithRealDatabase
{
    public function testHtmlDataAttributesForSpecificDeviceProperties_GivenDeviceAddedToDatabase_AttributesMatchNameAndValueOfAddedDevice(): void
    {
        foreach ($this->deviceTypeConstants() as $specificDeviceType) {
            $addedDevice = $this->addDeviceToDatabase($specificDeviceType);
            $specificDevice = $this->addSpecificDevice($addedDevice->id, $specificDeviceType);
            $specificDeviceProperties = $specificDevice->getFillable();
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

    private function addDeviceToDatabase(int $deviceType): Device
    {
        return factory(Device::class)->create([
            'user_id' => self::$faker->randomNumber(),
            'device_type_id' => $deviceType
        ]);
    }

    private function assertHtmlAttributesMatchSpecificDeviceProperties(Model $specificDevice, array $attributeNames, array $attributeValues, array $specificDeviceProperties): void
    {
        $this->assertEquals(sizeof($attributeNames), sizeof($specificDeviceProperties));

        for ($i = 0; $i < sizeof($attributeNames); $i++) {
            $this->assertEquals($specificDeviceProperties[$i], $attributeNames[$i]);
            $this->assertEquals($specificDevice->{$attributeNames[$i]}, $attributeValues[$i]);
        }
    }

    private function addSpecificDevice(int $deviceId, int $specificDeviceType): RFDevice
    {
        if ($specificDeviceType == DeviceTypes::RF_DEVICE) {
            $specificDevice = $this->addRFDeviceToDatabase($deviceId);
        }

        return $specificDevice;
    }

    private function addRFDeviceToDatabase(int $deviceId): RFDevice
    {
        return factory(RFDevice::class)->create([
            'device_id' => $deviceId
        ]);
    }

    private function deviceTypeConstants(): array
    {
        $deviceTypesClass = new \ReflectionClass(DeviceTypes::class);
        $specificDeviceTypes = $deviceTypesClass->getConstants();

        return $specificDeviceTypes;
    }
}
