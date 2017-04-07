<?php

namespace App\Http\Controllers\Web;

use App\Device;
use App\Http\Controllers\Common\Controller;
use App\Http\Globals\DeviceTypes;
use App\Http\Globals\FlashMessageLevels;
use App\Http\MQTT\MessagePublisher;
use App\RFDevice;
use App\User;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DevicesController extends Controller
{
    private $deviceModel;
    private $rfDeviceModel;
    private $userModel;
    private $messagePublisher;

    public function __construct(Device $deviceModel, RFDevice $rfDeviceModel, User $userModel, MessagePublisher $messagePublisher)
    {
        $this->middleware('guest');

        $this->deviceModel = $deviceModel;
        $this->rfDeviceModel = $rfDeviceModel;
        $this->userModel = $userModel;
        $this->messagePublisher = $messagePublisher;
    }

    public function devices() : View
    {
        $currentUser = $this->currentUser();

        return view('devices', [
            'name' => $currentUser->name,
            'devices' => $currentUser->devices
        ]);
    }

    public function add(Request $request) : RedirectResponse
    {
        $name = $request->input('name');
        $description = $request->input('description');
        $onCode = $request->input('onCode');
        $offCode = $request->input('offCode');
        $pulseLength = $request->input('pulseLength');
        $type = DeviceTypes::RF_DEVICE;

        $currentUserId = $this->currentUser()->id;
        $newDeviceId = $this->deviceModel->add($name, $description, $type, $currentUserId)->id;
        $this->rfDeviceModel->add($onCode, $offCode, $pulseLength, $newDeviceId);

        $request->session()->flash(FlashMessageLevels::SUCCESS, "Device '$name' was successfully added!");

        return redirect()->route('devices');
    }

    public function delete(Request $request, int $id) : RedirectResponse
    {
        $doesUserOwnDevice = $this->currentUser()->doesUserOwnDevice($id);

        if (!$doesUserOwnDevice) {
            $request->session()->flash(FlashMessageLevels::DANGER, 'Error deleting device!');

            return redirect()->route('devices');
        }

        $name = $this->deviceModel->find($id)->name;

        $this->deviceModel->destroy($id);

        $request->session()->flash(FlashMessageLevels::SUCCESS, "Device '$name' was successfully deleted!");

        return redirect()->route('devices');
    }

    public function handleControlRequest(Request $request, string $action, int $deviceId) : RedirectResponse
    {
        $currentUser = $this->currentUser();
        $doesUserOwnDevice = $currentUser->doesUserOwnDevice($deviceId);

        if (!$doesUserOwnDevice) {
            $request->session()->flash(FlashMessageLevels::DANGER, 'Error controlling device!');

            return redirect()->route('devices');
        }

        $this->messagePublisher->publish($currentUser->user_id, $deviceId, $action);

        return redirect()->route('devices');
    }

    private function currentUser() : User
    {
        $userId = session(env('SESSION_USER_ID'));
        $currentUser = $this->userModel->where('user_id', $userId)->first();

        return $currentUser;
    }
}
