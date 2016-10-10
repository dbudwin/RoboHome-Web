<?php

namespace Controllers;

class DeviceController extends Controller {
    protected $db;
    protected $devicesModel;
    protected $rfDeviceModel;
    protected $userDevicesModel;
    protected $userDevicesViewModel;
    
    function __construct() {
        parent::__construct();
        $db = $this->db;
        $this->devicesModel = new \Models\DevicesModel($db);
        $this->rfDeviceModel = new \Models\RFDeviceModel($db);
        $this->userDevicesModel = new \Models\UserDevicesModel($db);
        $this->userDevicesViewModel = new \Models\UserDevicesViewModel($db);
    }

    function devices($f3) {
        $currentUser = $this->currentUser($f3);
        $devicesForCurrentUser = $this->userDevicesViewModel->devicesForUser($currentUser->ID);
        $f3->set("name", $currentUser->Name);
        $f3->set("devices", $devicesForCurrentUser);
        $template = new \Template;
        echo $template->render("devices.html");
    }

    function add($f3) {
        $currentUserId = $this->currentUser($f3)->ID;
        $deviceId = $this->devicesModel->add();
        $this->rfDeviceModel->add($deviceId);
        $this->userDevicesModel->add($currentUserId, $deviceId);
        $f3->reroute("@devices");
    }

    function delete($f3, $args) {
        $currentUserId = $this->currentUser($f3)->ID;
        $deviceId = $args["id"];

        $doesUserOwnDevice = $this->userDevicesViewModel->doesUserOwnDevice($currentUserId, $deviceId);

        if ($doesUserOwnDevice) {
            $this->userDevicesModel->delete($deviceId);
            $this->rfDeviceModel->delete($deviceId);
            $this->devicesModel->delete($deviceId);
        }

        $f3->reroute("@devices");
    }

    private function currentUser($f3)
    {
        $userModel = new \Models\UserModel($this->db);
        $currentUser = $userModel->findUser($f3->get("SESSION.user"))[0];

        return $currentUser;
    }
}
