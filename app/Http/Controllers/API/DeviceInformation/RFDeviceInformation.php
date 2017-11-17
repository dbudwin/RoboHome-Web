<?php

namespace App\Http\Controllers\API\DeviceInformation;

use App\Repositories\IRFDeviceRepository;
use Illuminate\Http\JsonResponse;

class RFDeviceInformation implements IDeviceInformation
{
    private $rfDeviceRepository;

    public function __construct(IRFDeviceRepository $rfDeviceRepository)
    {
        $this->rfDeviceRepository = $rfDeviceRepository;
    }

    public function info(int $deviceId, string $action): JsonResponse
    {
        $rfDevice = $this->rfDeviceRepository->get($deviceId);

        $response = null;

        $action = strtolower($action);

        if ($action === 'turnon') {
            $response = [
                'code' => $rfDevice->on_code
            ];
        } elseif ($action === 'turnoff') {
            $response = [
                'code' => $rfDevice->off_code
            ];
        }

        if ($response === null) {
            return response()->json(['error' => "Device does not support action '$action'"], 400);
        }

        return response()->json($response);
    }
}
