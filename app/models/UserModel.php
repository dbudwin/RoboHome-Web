<?php

namespace Models;

class UserModel extends \DB\SQL\Mapper
{
    public function __construct(\DB\SQL $db)
    {
        parent::__construct($db, "Users");
    }

    public function findUser($id)
    {
        $this->load(array("UserID = ?", $id));
        return $this->query;
    }

    public function add($name, $email, $userId)
    {
        $this->Name = $name;
        $this->Email = $email;
        $this->UserID = $userId;
        $this->save();
    }

    public function delete($id)
    {
        $this->load(array("ID = ?", $id));
        $this->erase();
    }
}
