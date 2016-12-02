<?php

namespace API\Controllers;

class DeviceController extends \Common\Controllers\Controller
{
    private $userDevicesViewModel;

    public function __construct(\Base $f3)
    {
        parent::__construct($f3);

        $this->userDevicesViewModel = $this->container->get('UserDevicesViewModel');
    }

    public function devices($f3)
    {
        $devicesForCurrentUser = $this->userDevicesViewModel->devicesForUser($this->currentUserId($f3));

        $response = [
            'header' => $this->createHeader('DiscoverAppliancesResponse', 'Alexa.ConnectedHome.Discovery'),
            'payload' => [
                'discoveredAppliances' => $this->buildAppliancesJson($devicesForCurrentUser)
            ]
        ];

        echo $this->createJsonResponse($response);
    }

    public function turnOn($f3)
    {
        $deviceId = $_POST['id'];
        $doesUserOwnDevice = $this->userDevicesViewModel->doesUserOwnDevice($this->currentUserId($f3), $deviceId);

        if ($doesUserOwnDevice) {
            echo $this->createJsonResponse(['payload' => (object)[]]);
        }
    }

    public function turnOff($f3)
    {
        $deviceId = $_POST['id'];
        $doesUserOwnDevice = $this->userDevicesViewModel->doesUserOwnDevice($this->currentUserId($f3), $deviceId);

        if ($doesUserOwnDevice) {
            echo $this->createJsonResponse(['payload' => (object)[]]);
        }
    }

    private function buildAppliancesJson($devicesForCurrentUser)
    {
        $actions = ['turnOn', 'turnOff'];
        
        $appliances = [];

        for ($i = 0; $i < count($devicesForCurrentUser); $i++) {
            $appliance = [
                'actions' => $actions,
                'additionalApplianceDetails' => (object)[],
                'applianceId' => $devicesForCurrentUser[$i]->DeviceID,
                'friendlyName' => $devicesForCurrentUser[$i]->Devices_Name,
                'friendlyDescription' => $devicesForCurrentUser[$i]->Description,
                'isReachable' => true,
                'manufacturerName' => 'N/A',
                'modelName' => 'N/A',
                'version' => 'N/A'
            ];

            array_push($appliances, $appliance);
        }

        return $appliances;
    }

    private function createHeader($responseName, $namespace)
    {
        $messageId = $_SERVER['HTTP_MESSAGE_ID'];

        $header = [
            'messageId' => $messageId,
            'name' => $responseName,
            'namespace' => $namespace,
            'payloadVersion' => '2'
        ];

        return $header;
    }

    private function currentUserId($f3)
    {
        $loginController = new LoginController($f3);

        $httpAuthorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
        $currentUserId = $loginController->validateUser($httpAuthorizationHeader);

        return $currentUserId;
    }
    
    private function createJsonResponse($body)
    {
        header('Content-Type: application/json');

        return json_encode($body);
    }
}
