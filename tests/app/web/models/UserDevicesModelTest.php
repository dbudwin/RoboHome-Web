<?php

namespace Tests\Models;

use Models\DevicesModel;
use Models\UserDevicesModel;
use Models\UserModel;

class UserDevicesModelTest extends BaseModelTest
{
    private $devicesModel;
    private $userDevicesModel;
    private $userModel;
    private $deviceId;
    private $name;
    private $email;
    private $userId;

    public function setUp()
    {
        $connection = $this->getConnection();

        $this->devicesModel = new DevicesModel($connection);
        $this->userDevicesModel = new UserDevicesModel($connection);
        $this->userModel = new UserModel($connection);

        $this->name = $this->faker->name;
        $this->email = $this->faker->email;

        $this->addUser();
        $this->addDevice();
    }

    public function testAdd()
    {
        $this->userDevicesModel->add($this->userId, $this->deviceId);

        $this->assertEquals(1, $this->userDevicesModel->ID);
        $this->assertEquals($this->userId, $this->userDevicesModel->ID);
        $this->assertEquals($this->deviceId, $this->userDevicesModel->DeviceID);
    }

    public function testDelete()
    {
        $this->userDevicesModel->add($this->userId, $this->deviceId);

        $this->userDevicesModel->delete($this->userId);

        $this->assertEquals(0, $this->userDevicesModel->count(array('ID = ?', $this->userDevicesModel->ID)));
    }

    private function addUser()
    {
        $userIdToken = 'amzn1.application-oa2-client.' . $this->faker->md5;
        $this->userModel->add($this->name, $this->email, $userIdToken);
        $this->userId = $this->userModel->ID;
    }

    private function addDevice()
    {
        $_POST = array('Name' => $this->name, 'Description' => $this->faker->text);

        $this->deviceId = $this->devicesModel->add();
    }
}
