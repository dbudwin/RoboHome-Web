<?php

namespace Tests\Models;

use Models\DevicesModel;

class DevicesModelTest extends BaseModelTest
{
    private $description;
    private $devicesModel;
    private $name;

    public function setUp()
    {
        $this->devicesModel = new DevicesModel($this->getConnection());
        $this->name = $this->faker->name;
        $this->description = $this->faker->text;

        $_POST = array('Name' => $this->name, 'Description' => $this->description);
    }

    public function testAdd()
    {
        $newId = $this->devicesModel->add();

        $this->assertEquals(1, $newId);
        $this->assertEquals($this->name, $this->devicesModel->Name);
        $this->assertEquals($this->description, $this->devicesModel->Description);
        $this->assertEquals(1, $this->devicesModel->Type);
    }

    public function testDelete()
    {
        $newId = $this->devicesModel->add();

        $this->devicesModel->delete($newId);

        $this->assertEquals(0, $this->devicesModel->count(array('ID = ?', $newId)));
    }
}
