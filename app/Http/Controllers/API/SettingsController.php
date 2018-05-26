<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Common\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function mqtt(Request $request): JsonResponse
    {
        $currentUser = $request->user();
        $publicUserId = $currentUser->public_id;
        $allDevicesForUserTopic = "RoboHome/$publicUserId/+";

        $response = [
            'mqtt' => [
                'server' => getenv('MQTT_SERVER'),
                'tlsPort' => getenv('MQTT_TLS_PORT'),
                'user' => getenv('MQTT_USER'),
                'password' => getenv('MQTT_PASSWORD'),
                'topic' => $allDevicesForUserTopic
            ]
        ];

        return response()->json($response);
    }
}
