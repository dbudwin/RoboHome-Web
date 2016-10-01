<?php

class DeviceController extends Controller {
    function devices($f3, $args) {
        $currentUser = $this->currentUser($f3);
        $devicesForCurrentUser = $this->devicesForUser($currentUser->ID);
        $f3->set("name", $currentUser->Name);
        $f3->set("devices", $devicesForCurrentUser);
        $template = new Template;
        echo $template->render("devices.html");
    }

    function add($f3) {
        $db = $this->db;
        $devicesModel = new DevicesModel($db);
        $deviceId = $devicesModel->add();
        $currentUserId = $this->currentUser($f3)->ID;
        $userDevicesModel = new UserDevicesModel($db);
        $userDevicesModel->add($currentUserId, $deviceId);
        $f3->reroute("@devices");
    }

    function delete($f3, $args) {
        $db = $this->db;
        $deviceId = $args["id"];
        $userDevicesModel = new UserDevicesModel($db);
        $userDevicesModel->delete($deviceId);
        $devicesModel = new DevicesModel($db);
        $devicesModel->delete($deviceId);
        $f3->reroute("@devices");
    }

    private function devicesForUser($userId) {
        $userDevicesView = new DB\SQL\Mapper($this->db, "UserDevicesView");
        $devicesForUser = $userDevicesView->find(array("UserDevices_UserID = ?", $userId));

        return $devicesForUser;
    }

    private function currentUser($f3) {
        $db = $this->db;
        $userModel = new UserModel($db);
        $currentUser = $userModel->findUser($f3->get("SESSION.user"))[0];

        return $currentUser;
    }
}
