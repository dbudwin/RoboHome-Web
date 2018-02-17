<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Common\Controller;
use App\Http\Globals\FlashMessageLevels;
use App\Http\MQTT\MessagePublisher;
use App\Repositories\IDeviceRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        $device = $this->deviceRepository->create($properties, $currentUserId);
        $name = $device->name;

        return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::SUCCESS, "Device '$name' was successfully added!");
    }

    public function delete(Request $request, int $id): RedirectResponse
    {
        $userOwnsDevice = $request->user()->ownsDevice($id);

        if (!$userOwnsDevice) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, 'Error deleting device!');
        }

        $name = $this->deviceRepository->name($id);
        $deleted = $this->deviceRepository->delete($id);

        if (!$deleted) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, "Encountered an error while deleting device '$name'!");
        }

        return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::SUCCESS, "Device '$name' was successfully deleted!");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $userOwnsDevice = $request->user()->ownsDevice($id);

        if (!$userOwnsDevice) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, 'Error updating device!');
        }

        $properties = $request->all();

        $device = $this->deviceRepository->update($id, $properties);

        return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::SUCCESS, "Device '$device->name' was successfully updated!");
    }

    public function handleControlRequest(Request $request, string $action, int $deviceId): RedirectResponse
    {
        $currentUser = $request->user();
        $userOwnsDevice = $currentUser->ownsDevice($deviceId);

        if (!$userOwnsDevice) {
            return $this->redirectToDevicesWithMessage($request, FlashMessageLevels::DANGER, 'Error controlling device!');
        }

        $this->messagePublisher->publish($currentUser->id, $action, $deviceId);

        return redirect()->route('devices');
    }

    private function redirectToDevicesWithMessage(Request $request, string $flashLevel, string $message): RedirectResponse
    {
        $request->session()->flash($flashLevel, $message);

        return redirect()->route('devices');
    }
}
