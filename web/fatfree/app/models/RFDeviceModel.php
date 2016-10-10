<?php

namespace Models;

class RFDeviceModel extends \DB\SQL\Mapper
{
    public function __construct(\DB\SQL $db)
    {
        parent::__construct($db, "RFDevice");
    }

    public function add($id) {
        $this->copyFrom("POST");
        $this->DeviceID = $id;
        $this->save();
    }

    public function delete($id) {
        $rfDevice = $this->load(array("DeviceID = ?", $id));
        $this->load(array("ID = ?", $rfDevice->ID));
        $this->erase();
    }
}