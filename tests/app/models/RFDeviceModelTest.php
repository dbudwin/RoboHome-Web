<?php

namespace Tests\Models;

use Models\DevicesModel;
use Models\RFDeviceModel;

class RFDeviceModelTest extends BaseModelTest
{
    private $devicesModel;
    private $rfDeviceModel;
    private $onCode;
    private $offCode;
    private $pulseLength;
    private $newDeviceId;

    public function setUp()
    {
        $connection = $this->getConnection();

        $this->devicesModel = new DevicesModel($connection);
        $this->rfDeviceModel = new RFDeviceModel($connection);

        $this->onCode = $this->faker->randomNumber;
        $this->offCode = $this->faker->randomNumber;
        $this->pulseLength = $this->faker->randomNumber;

        $this->addDevice();

        $_POST = array('OnCode' => $this->onCode, 'OffCode' => $this->offCode, 'PulseLength' => $this->pulseLength);
    }

    public function testAdd()
    {
        $this->rfDeviceModel->add($this->newDeviceId);

        $this->assertEquals(1, $this->rfDeviceModel->ID);
        $this->assertEquals($this->onCode, $this->rfDeviceModel->OnCode);
        $this->assertEquals($this->offCode, $this->rfDeviceModel->OffCode);
        $this->assertEquals($this->pulseLength, $this->rfDeviceModel->PulseLength);
    }

    public function testDelete()
    {
        $this->rfDeviceModel->add($this->newDeviceId);

        $this->rfDeviceModel->delete($this->newDeviceId);

        $this->assertEquals(0, $this->rfDeviceModel->count(array('DeviceID = ?', $this->newDeviceId)));
    }

    private function addDevice()
    {
        $name = $this->faker->name;
        $description = $this->faker->text;

        $_POST = array('Name' => $name, 'Description' => $description);
        
        $this->newDeviceId = $this->devicesModel->add();
    }
}
