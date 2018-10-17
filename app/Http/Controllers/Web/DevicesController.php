<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Common\Controller;
use App\Http\Globals\FlashMessageLevels;
use App\Http\MQTT\MessagePublisher;
use App\Repositories\IDeviceRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webpatser\Uuid\Uuid;
use Validator;
use Illuminate\Validation\Rule;

class DevicesController extends Controller
{
    private $deviceRepository;
    private $messagePublisher;

    public function __construct(IDeviceRepository $deviceRepository, MessagePublisher $messagePublisher)
    {
        $this->middleware('auth');

        $this->deviceRepository = $deviceRepository;
        $this->messagePublisher = $messagePublisher;
    }

    public function devices(Request $request): View
    {
        $currentUser = $request->user();

        return view('devices', [
            'name' => $currentUser->name,
            'devices' => $currentUser->devices
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $properties = $request->all();
        $currentUserId = $request->user()->id;

        $data = array_merge($properties, ['user_id' => $currentUserId]);
        $validator = Validator::make( $data, [
            'name' => [
                'required',
                Rule::unique('devices')->where(function ($query) use ($data) {
                    return $query->where('name', $data['name'])
                                ->where('user_id', $data['user_id']);
                })
            ],
        ]);
        if ($validator->fails()) {
            $name = $properties['name'];
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, "Device '$name' has existed!");
        }

        $device = $this->deviceRepository->create($properties, $currentUserId);
        $name = $device->name;

        return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::SUCCESS, "Device '$name' was successfully added!");
    }

    public function delete(Request $request, Uuid $publicDeviceId): RedirectResponse
    {
        $deviceId = $this->deviceRepository->getForPublicId($publicDeviceId)->id;

        $userOwnsDevice = $request->user()->ownsDevice($deviceId);

        if (!$userOwnsDevice) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, 'Error deleting device!');
        }

        $name = $this->deviceRepository->name($deviceId);
        $deleted = $this->deviceRepository->delete($deviceId);

        if (!$deleted) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, "Encountered an error while deleting device '$name'!");
        }

        return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::SUCCESS, "Device '$name' was successfully deleted!");
    }

    public function update(Request $request, Uuid $publicDeviceId): RedirectResponse
    {
        $deviceId = $this->deviceRepository->getForPublicId($publicDeviceId)->id;

        $userOwnsDevice = $request->user()->ownsDevice($deviceId);

        if (!$userOwnsDevice) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, 'Error updating device!');
        }

        $properties = $request->all();
        $currentUserId = $request->user()->id;

        $data = array_merge($properties, ['user_id' => $currentUserId, 'device_id' => $deviceId]);
        $validator = Validator::make( $data, [
            'name' => [
                'required',
                Rule::unique('devices')->where(function ($query) use ($data) {
                    return $query->where('name', $data['name'])
                                ->where('user_id', $data['user_id'])
                                ->where('id', '!=', $data['device_id']);
                })
            ],
        ]);
        if ($validator->fails()) {
            $name = $properties['name'];
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, "Device '$name' has existed!");
        }

        $device = $this->deviceRepository->update($deviceId, $properties);

        return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::SUCCESS, "Device '$device->name' was successfully updated!");
    }

    public function handleControlRequest(Request $request, string $action, Uuid $publicDeviceId): RedirectResponse
    {
        $deviceId = $this->deviceRepository->getForPublicId($publicDeviceId)->id;
        $currentUser = $request->user();
        $userOwnsDevice = $currentUser->ownsDevice($deviceId);

        if (!$userOwnsDevice) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, 'Error controlling device!');
        }

        $this->messagePublisher->publish($action, Uuid::import($currentUser->public_id), Uuid::import($publicDeviceId));

        return redirect()->route('devices');
    }

    private function redirectToDevicesWithMessage(Request $request, string $flashLevel, string $message): RedirectResponse
    {
        $request->session()->flash($flashLevel, $message);

        return redirect()->route('devices');
    }
}
