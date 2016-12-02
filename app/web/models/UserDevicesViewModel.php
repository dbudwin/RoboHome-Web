<?php

namespace Models;

class UserDevicesViewModel extends \DB\SQL\Mapper
{
    public function __construct(\DB\SQL $db)
    {
        parent::__construct($db, 'UserDevicesView');
    }

    public function devicesForUser($userId)
    {
        $devicesForUser = $this->find(array('UserDevices_UserID = ?', $userId));

        return $devicesForUser;
    }

    public function doesUserOwnDevice($userId, $deviceId)
    {
        $this->load(array('UserDevices_UserID = ? AND DeviceID = ?', $userId, $deviceId));

        return !$this->dry();
    }
}
