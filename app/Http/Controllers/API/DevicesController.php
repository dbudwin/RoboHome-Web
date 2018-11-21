<?php

namespace App\Http\Controllers\API;

use App\DeviceActionInfo\IDeviceActionInfoBroker;
use App\Http\Controllers\Common\Controller;
use App\Http\Globals\DeviceActions;
use App\Http\MQTT\MessagePublisher;
use App\Repositories\IDeviceRepository;
use Carbon\Carbon;
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
            'event' => [
                'header' => $this->createHeader($request, 'Discover.Response', 'Alexa.Discovery'),
                'payload' => [
                    'endpoints' => $this->buildDiscoverEndpointsJsonResponse($devicesForCurrentUser)
                ]
            ]
        ];

        return response()->json($response);
    }

    public function info(Request $request): JsonResponse
    {
        $publicDeviceId = Uuid::import($request->input('publicDeviceId'));
        $action = $request->get('action');
        $device = $this->deviceRepository->getForPublicId($publicDeviceId);

        return $this->deviceInformationBroker->infoNeededToPerformDeviceAction($device, $action);
    }

    public function control(Request $request, string $action): JsonResponse
    {
        $user = $request->user();
        $publicUserId = Uuid::import($user->public_id);
        $publicDeviceId = Uuid::import($request->input('publicDeviceId'));

        $urlValidAction = strtolower($action);

        $published = $this->messagePublisher->publish($urlValidAction, $publicUserId, $publicDeviceId);

        if (!$published) {
            return response()->json(['error' => 'Message not published'], 500);
        }

        return $this->buildControlEndpointJsonResponse($request, $action, $publicDeviceId);
    }

    private function buildControlEndpointJsonResponse(Request $request, string $action, string $publicDeviceId): JsonResponse
    {
        $authorizationHeader = $request->header('Authorization');

        $response = [
            'context' => [
                'properties' => array([
                    'namespace' => 'Alexa.PowerController',
                    'name' => 'powerState',
                    'value' => DeviceActions::actionToDirectiveName($action),
                    'timeOfSample' => Carbon::now()->toIso8601String(),
                    'uncertaintyInMilliseconds' => 50
                ])
            ],
            'event' => [
                'header' => $this->createHeader($request, 'Response', 'Alexa'),
                'endpoint' => [
                    'scope' => [
                        'type' => 'BearerToken',
                        'token' => $this->extractAuthorizationTokenFromHeader($authorizationHeader)
                    ],
                    'endpointId' => $publicDeviceId
                ],
                'payload' => (object)[]
            ]
        ];

        return response()->json($response);
    }

    private function buildDiscoverEndpointsJsonResponse($devicesForCurrentUser): array
    {
        $endpoints = [];

        foreach ($devicesForCurrentUser as $device) {
            $endpoint = [
                'endpointId' => $device->public_id,
                'friendlyName' => $device->name,
                'description' => $device->description,
                'manufacturerName' => 'N/A',
                'displayCategories' => array('LIGHT'),
                'capabilities' => array([
                    'type' => 'AlexaInterface',
                    'interface' => 'Alexa.PowerController',
                    'version' => '3'
                ])
            ];

            array_push($endpoints, $endpoint);
        }

        return $endpoints;
    }

    private function createHeader(Request $request, string $directive, string $namespace): array
    {
        $messageId = $request->header('Message-Id');

        $header = [
            'namespace' => $namespace,
            'name' => $directive,
            'messageId' => $messageId,
            'payloadVersion' => '3'
        ];

        return $header;
    }

    private function extractAuthorizationTokenFromHeader(string $authorizationHeader): string
    {
        if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
