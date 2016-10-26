<?php

namespace Test\Models;

use Models\DevicesModel;

class DevicesModelTest extends BaseModelTest
{
    private $deviceModel;

    public function setUp()
    {
        $this->deviceModel = new DevicesModel($this->getConnection());
    }

    public function testAdd()
    {
        $_POST = array('Name' => 'dummy', 'Description' => 'lorem ipsum', 'Type' => 1);

        $newId = $this->deviceModel->add();

        $this->assetEqual(1, $newId);
        $this->assetEqual('dummy', $this->deviceModel->Name);
        $this->assetEqual('lorem ipsum', $this->deviceModel->Description);
        $this->assetEqual(1, $this->deviceModel->Type);
    }

    public function testDelete()
    {
        $newId = $this->deviceModel->add();
        $this->deviceModel->delete($newId);

        $this->assetEqual(false, $this->deviceModel->findone(['id' => $newId]));
    }
}

