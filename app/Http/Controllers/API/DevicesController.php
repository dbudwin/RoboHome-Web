<?php

namespace App\Http\Controllers\API;

use App\Device;
use App\Http\Controllers\Common\Controller;
use App\User;
use Illuminate\Http\Request;

class DevicesController extends Controller
{
    private $deviceModel;
    private $userModel;

    public function __construct(Device $deviceModel, User $userModel)
    {
        $this->middleware('apiAuthenticator');

        $this->deviceModel = $deviceModel;
        $this->userModel = $userModel;
    }

    public function index(Request $request)
    {
        $userId = $request->get('currentUserId');

        $devicesForCurrentUser = $this->currentUser($userId)->devices;

        $response = [
            'header' => $this->createHeader($request, 'DiscoverAppliancesResponse', 'Alexa.ConnectedHome.Discovery'),
            'payload' => [
                'discoveredAppliances' => $this->buildAppliancesJson($devicesForCurrentUser)
            ]
        ];

        return response()->json($response);
    }

    public function turnOn(Request $request)
    {
        $response = $this->handleControlRequest($request, 'TurnOnConfirmation');

        return $response;
    }

    public function turnOff(Request $request)
    {
        $response = $this->handleControlRequest($request, 'TurnOffConfirmation');

        return $response;
    }

    private function handleControlRequest(Request $request, $responseName)
    {
        $userId = $request->get('currentUserId');
        $deviceId = $request->input('id');

        $doesUserOwnDevice = $this->currentUser($userId)->doesUserOwnDevice($deviceId);

        if (!$doesUserOwnDevice) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = [
            'header' => $this->createHeader($request, $responseName, 'Alexa.ConnectedHome.Control'),
            'payload' => (object)[]
        ];

        return response()->json($response);
    }

    private function buildAppliancesJson($devicesForCurrentUser)
    {
        $actions = ['turnOn', 'turnOff'];

        $appliances = [];

        for ($i = 0; $i < count($devicesForCurrentUser); $i++) {
            $appliance = [
                'actions' => $actions,
                'additionalApplianceDetails' => (object)[],
                'applianceId' => $devicesForCurrentUser[$i]->id,
                'friendlyName' => $devicesForCurrentUser[$i]->name,
                'friendlyDescription' => $devicesForCurrentUser[$i]->description,
                'isReachable' => true,
                'manufacturerName' => 'N/A',
                'modelName' => 'N/A',
                'version' => 'N/A'
            ];

            array_push($appliances, $appliance);
        }

        return $appliances;
    }

    private function createHeader(Request $request, $responseName, $namespace)
    {
        $messageId = $request->header('Message-Id');

        $header = [
            'messageId' => $messageId,
            'name' => $responseName,
            'namespace' => $namespace,
            'payloadVersion' => '2'
        ];

        return $header;
    }

    private function currentUser($userId)
    {
        $currentUser = $this->userModel->where('user_id', $userId)->first();

        return $currentUser;
    }
}
