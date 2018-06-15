<?php

namespace App\Http\Controllers\API;

use App\Device;
use App\DeviceActionInfo\IDeviceActionInfoBroker;
use App\Http\Controllers\Common\Controller;
use App\Http\Globals\DeviceActions;
use App\Http\MQTT\MessagePublisher;
use App\Repositories\IDeviceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

class DevicesController extends Controller
{
    private $deviceRepository;
    private $deviceInformationBroker;
    private $messagePublisher;

    public function __construct(IDeviceRepository $deviceRepository, IDeviceActionInfoBroker $deviceInformationBroker, MessagePublisher $messagePublisher)
    {
        $this->middleware('auth:api');

        $this->deviceRepository = $deviceRepository;
        $this->deviceInformationBroker = $deviceInformationBroker;
        $this->messagePublisher = $messagePublisher;
    }

    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user();

        $devicesForCurrentUser = $currentUser->devices;

        $response = [
            'header' => $this->createHeader($request, 'DiscoverAppliancesResponse', 'Alexa.ConnectedHome.Discovery'),
            'payload' => [
                'discoveredAppliances' => $this->buildAppliancesJson($devicesForCurrentUser)
            ]
        ];

        return response()->json($response);
    }

    public function info(Request $request): JsonResponse
    {
        $user = $request->user();
        $publicDeviceId = $request->get('publicDeviceId');
        $action = $request->get('action');
        $device = $this->deviceRepository->getForPublicId(Uuid::import($publicDeviceId));

        $userOwnsDevice = $user->ownsDevice($device->id);

        if (!$userOwnsDevice) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->deviceInformationBroker->infoNeededToPerformDeviceAction($device, $action);
    }

    public function control(Request $request, string $action): JsonResponse
    {
        $user = $request->user();
        $publicUserId = Uuid::import($user->public_id);
        $publicDeviceId = Uuid::import($request->input('publicDeviceId'));
        $deviceId = $this->deviceRepository->getForPublicId($publicDeviceId)->id;

        $userOwnsDevice = $user->ownsDevice($deviceId);

        if (!$userOwnsDevice) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $urlValidAction = strtolower($action);

        $published = $this->messagePublisher->publish($urlValidAction, $publicUserId, $publicDeviceId);

        if (!$published) {
            return response()->json(['error' => 'Message not published'], 500);
        }

        return $this->buildControlJson($request, $action);
    }

    private function buildControlJson(Request $request, string $action): JsonResponse
    {
        $response = [
            'header' => $this->createHeader($request, DeviceActions::actionToConfirmationName($action), 'Alexa.ConnectedHome.Control'),
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
