<?php

use Models\DevicesModel;

class DeicesModelTest extends BaseModelTest
{
    public function testConstruct()
    {
        $deviceModel = new DevicesModel($this->getConnection());
    }

    public function testAdd()
    {
        $_POST = array('Name' => 'dummy', 'Description' => 'lorem ipsum', 'Type' => 1);

        $deviceModel = new DevicesModel($this->getConnection());

        $newId = $deviceModel->add();

        $this->assetEqual(1, $newId);
        $this->assetEqual('dummy', $deviceModel->Name);
        $this->assetEqual('lorem ipsum', $deviceModel->Description);
        $this->assetEqual(1, $deviceModel->Type);
    }

    public function testDelete()
    {
        $_POST = array('Name' => 'dummy', 'Description' => 'lorem ipsum', 'type' => 1);

        $deviceModel = new DevicesModel($this->getConnection());

        $newId = $deviceModel->add();
        $deviceModel->delete($newId);

        $this->assetEqual(false, $deviceModel->findone(['id' => $newId]));
    }

}