<?php

namespace App\Http\Middleware;

use App\Repositories\IDeviceRepository;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

class ApiVerifyUserOwnsDevice
{
    private $deviceRepository;

    public function __construct(IDeviceRepository $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function handle(Request $request, Closure $next): JsonResponse
    {
        $user = $request->user();
        $publicDeviceId = $request->get('publicDeviceId');

        $device = $this->deviceRepository->getForPublicId(Uuid::import($publicDeviceId));

        $userOwnsDevice = $user->ownsDevice($device->id);

        if (!$userOwnsDevice) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
