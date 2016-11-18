<?php

namespace Controllers;

class DeviceController extends Controller
{
    protected $devicesModel;
    protected $rfDeviceModel;
    protected $userDevicesModel;
    protected $userDevicesViewModel;

    public function __construct(\Base $f3)
    {
        parent::__construct($f3);
        
        $this->devicesModel = $this->container->get('DevicesModel');
        $this->rfDeviceModel = $this->container->get('RFDeviceModel');
        $this->userDevicesModel = $this->container->get('UserDevicesModel');
        $this->userDevicesViewModel = $this->container->get('UserDevicesViewModel');
    }

    public function devices($f3)
    {
        $currentUser = $this->currentUser($f3);
        $devicesForCurrentUser = $this->userDevicesViewModel->devicesForUser($currentUser->ID);
        $f3->set('name', $currentUser->Name);
        $f3->set('devices', $devicesForCurrentUser);
        $template = new \Template;
        echo $template->render('devices.html');
    }

    public function add($f3)
    {
        $currentUserId = $this->currentUser($f3)->ID;
        $deviceId = $this->devicesModel->add();
        $this->rfDeviceModel->add($deviceId);
        $this->userDevicesModel->add($currentUserId, $deviceId);
        $f3->reroute('@devices');
    }

    public function delete($f3, $args)
    {
        $currentUserId = $this->currentUser($f3)->ID;
        $deviceId = $args['id'];

        $doesUserOwnDevice = $this->userDevicesViewModel->doesUserOwnDevice($currentUserId, $deviceId);

        if ($doesUserOwnDevice) {
            $this->userDevicesModel->delete($deviceId);
            $this->rfDeviceModel->delete($deviceId);
            $this->devicesModel->delete($deviceId);
        }

        $f3->reroute('@devices');
    }

    private function currentUser($f3)
    {
        $userModel = $this->container->get('UserModel');
        $currentUser = $userModel->findUser($f3->get('SESSION.user'))[0];

        return $currentUser;
    }
}
