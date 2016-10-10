<?php

namespace Models;

class UserDevicesViewModel extends \DB\SQL\Mapper
{
    protected $db;
    protected $userDevicesView;

    public function __construct(\DB\SQL $db)
    {
        parent::__construct($db, "UserDevicesView");
        $db = $this->db;
        $this->userDevicesView = new \DB\SQL\Mapper($this->db, "UserDevicesView");
    }

    public function devicesForUser($userId)
    {
        $devicesForUser = $this->userDevicesView->find(array("UserDevices_UserID = ?", $userId));

        return $devicesForUser;
    }

    public function doesUserOwnDevice($userId, $deviceId)
    {
        $this->userDevicesView->load(array("UserDevices_UserID = ? AND DeviceID = ?", $userId, $deviceId));

        return !$this->userDevicesView->dry();
    }
}