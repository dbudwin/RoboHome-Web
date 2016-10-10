<?php

namespace Models;

class UserDevicesModel extends \DB\SQL\Mapper
{
    public function __construct(\DB\SQL $db)
    {
        parent::__construct($db, "UserDevices");
    }

    public function add($userId, $deviceId)
    {
        $this->UserID = $userId;
        $this->DeviceID = $deviceId;
        $this->save();
    }

    public function delete($userId)
    {
        $userDevice = $this->load(array("DeviceID = ?", $userId));
        $this->load(array("ID = ?", $userDevice->ID));
        $this->erase();
    }
}