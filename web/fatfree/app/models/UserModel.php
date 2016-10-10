<?php

namespace Models;

class UserModel extends \DB\SQL\Mapper
{
    public function __construct(\DB\SQL $db)
    {
        parent::__construct($db, "Users");
    }

    public function all()
    {
        $this->load();
        return $this->query;
    }

    public function findUser($id)
    {
        $this->load(array("UserID = ?", $id));
        return $this->query;
    }

    public function add()
    {
        $this->copyFrom("POST");
        $this->save();
    }

    public function createNewUser($name, $email, $userId)
    {
        $this->Name = $name;
        $this->Email = $email;
        $this->UserID = $userId;
        $this->save();
    }
    
    public function edit($id)
    {
        $this->load(array("ID = ?", $id));
        $this->copyFrom("POST");
        $this->update();
    }
    
    public function delete($id)
    {
        $this->load(array("ID = ?", $id));
        $this->erase();
    }
}