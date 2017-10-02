<?php

namespace App\Http\Controllers\API;

use App\Device;
use App\Http\Controllers\API\DeviceInformation\IDeviceInformation;
use App\Http\Controllers\Common\Controller;
use App\Http\Controllers\Common\DeviceOwner;
use App\Http\Globals\DeviceActions;
use App\Http\MQTT\MessagePublisher;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DevicesController extends Controller
{

    use DeviceOwner;

    private $deviceModel;
    private $userModel;
    private $messagePublisher;
    private $deviceInformation;

    public function __construct(Device $deviceModel, User $userModel, MessagePublisher $messagePublisher, IDeviceInformation $deviceInformation)
    {
        $this->middleware('apiAuthenticator', ['except' => ['info']]);

        $this->deviceModel = $deviceModel;
        $this->userModel = $userModel;
        $this->messagePublisher = $messagePublisher;
        $this->deviceInformation = $deviceInformation;
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $request->get('currentUserId');

        // todo : is not secure to do like this, need to test user existence
        $devicesForCurrentUser = $this->getCurrentUser($userId)->devices;

        $response = [
            'header' => $this->createHeader($request, 'DiscoverAppliancesResponse', 'Alexa.ConnectedHome.Discovery'),
            'payload' => [
                'discoveredAppliances' => $this->buildAppliancesJson($devicesForCurrentUser)
            ]
        ];

        return response()->json($response);
    }

    public function turnOn(Request $request): JsonResponse
    {
        $response = $this->handleControlRequest($request, DeviceActions::TURN_ON, 'TurnOnConfirmation');

        return $response;
    }

    public function turnOff(Request $request): JsonResponse
    {
        $response = $this->handleControlRequest($request, DeviceActions::TURN_OFF, 'TurnOffConfirmation');

        return $response;
    }

    public function info(Request $request): JsonResponse
    {
        $userId = $request->get('userId');
        $deviceId = $request->get('deviceId');
        $action = $request->get('action');

        $user = $this->getCurrentUser($userId);
        if (!$user || $this->isDeviceOwner($user, $deviceId)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->deviceInformation->info($deviceId, $action);
    }

    private function handleControlRequest(Request $request, string $action, string $responseName): JsonResponse
    {
        $userId = $request->get('currentUserId');
        $deviceId = $request->input('id');

        $user = $this->getCurrentUser($userId);
        if (!$user || $this->isDeviceOwner($user, $deviceId)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $urlValidAction = strtolower($action);

        $this->messagePublisher->publish($userId, $urlValidAction, $deviceId);

        $response = [
            'header' => $this->createHeader($request, $responseName, 'Alexa.ConnectedHome.Control'),
            'payload' => (object)[]
        ];

        return response()->json($response);
    }

    private function buildAppliancesJson($devicesForCurrentUser): array
    {
        $actions = [DeviceActions::TURN_ON, DeviceActions::TURN_OFF];

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

    private function createHeader(Request $request, string $responseName, string $namespace): array
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
}
