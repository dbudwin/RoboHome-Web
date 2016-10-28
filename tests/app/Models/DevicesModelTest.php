<?php

namespace Test\Models;

use Models\DevicesModel;

class DevicesModelTest extends BaseModelTest
{
    private $devicesModel;

    public function setUp()
    {
        $_POST = array('Name' => 'dummy', 'Description' => 'lorem ipsum');

        $this->devicesModel = new DevicesModel($this->getConnection());
    }

    public function testAdd()
    {
        $newId = $this->devicesModel->add();

        $this->assertEquals(1, $newId);
        $this->assertEquals('dummy', $this->devicesModel->Name);
        $this->assertEquals('lorem ipsum', $this->devicesModel->Description);
        $this->assertEquals(1, $this->devicesModel->Type);
    }

    public function testDelete()
    {
        $newId = $this->devicesModel->add();

        $this->devicesModel->delete($newId);

        $this->assertEquals(0, $this->devicesModel->count(array('ID = ?', $newId)));
    }
}
