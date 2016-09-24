<?php

class DeviceController extends Controller {
    function devices($f3, $args) {
        $db = $this->db;
        $userModel = new UserModel($db);
        $currentUser = $userModel->findUser($f3->get("SESSION.user"))[0];
        $f3->set("name", $currentUser->Name);
        $template = new Template;
        echo $template->render("devices.html");
    }
}
