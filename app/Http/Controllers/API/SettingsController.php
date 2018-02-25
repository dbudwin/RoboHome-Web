<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Common\Controller;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function mqtt(): JsonResponse
    {
        $response = [
            'mqtt' => [
                'server' => getenv('MQTT_SERVER'),
                'tlsPort' => getenv('MQTT_TLS_PORT'),
                'user' => getenv('MQTT_USER'),
                'password' => getenv('MQTT_PASSWORD')
            ]
        ];

        return response()->json($response);
    }
}
