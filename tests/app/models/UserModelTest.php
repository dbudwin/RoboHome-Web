<?php

namespace Test\Models;

use Models\UserModel;

class UserModelTest extends BaseModelTest
{
    private $userModel;
    private $name;
    private $email;
    private $userId;

    public function setUp()
    {
        $this->userModel = new UserModel($this->getConnection());
        
        $this->name = $this->faker->name;
        $this->email = $this->faker->email;
        $this->userId = 'amzn1.application-oa2-client.' . $this->faker->md5;
    }

    public function testFindUser()
    {
        $this->userModel->add($this->name, $this->email, $this->userId);
        $userIdFromDb = $this->userModel->UserID;

        $userArray = $this->userModel->findUser($userIdFromDb);
        
        $user = $userArray[0];
        $this->assertEquals(1, count($userArray));
        $this->assertEquals(1, $user->ID);
        $this->assertEquals($this->name, $user->Name);
        $this->assertEquals($this->email, $user->Email);
        $this->assertEquals($this->userId, $user->UserID);
    }

    public function testAdd()
    {
        $this->userModel->add($this->name, $this->email, $this->userId);

        $this->assertEquals(1, $this->userModel->ID);
        $this->assertEquals($this->name, $this->userModel->Name);
        $this->assertEquals($this->email, $this->userModel->Email);
        $this->assertEquals($this->userId, $this->userModel->UserID);
    }

    public function testDelete()
    {
        $this->userModel->add($this->name, $this->email, $this->userId);

        $this->userModel->delete($id);

        $this->assertEquals(0, $this->userModel->count(array('ID = ?', $id)));
    }
}
