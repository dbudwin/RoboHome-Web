<?php

namespace Models;

class DevicesModel extends \DB\SQL\Mapper
{
    public function __construct(\DB\SQL $db)
    {
        parent::__construct($db, "Devices");
    }

    public function add()
    {
        $this->copyFrom("POST");
        $this->Type = 1;
        $this->save();
        $id = $this->ID;
        return $id;
    }

    public function delete($id)
    {
        $this->load(array("ID = ?", $id));
        $this->erase();
    }
}