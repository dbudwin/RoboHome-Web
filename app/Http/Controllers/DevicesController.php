<?php

namespace App\Http\Controllers;

use App\Device;
use App\RFDevice;
use App\User;
use DB;
use Illuminate\Http\Request;

class DevicesController extends Controller
{
    private $deviceModel;
    private $rfDeviceModel;
    private $userModel;

    public function __construct(Device $deviceModel, RFDevice $rfDeviceModel, User $userModel)
    {
        $this->middleware('guest');

        $this->deviceModel = $deviceModel;
        $this->rfDeviceModel = $rfDeviceModel;
        $this->userModel = $userModel;
    }

    public function devices()
    {
        $currentUser = $this->currentUser();

        return view('devices', [
            'name' => $currentUser->name,
            'devices' => $currentUser->devices
        ]);
    }

    public function add(Request $request)
    {
        $name = $request->input('name');
        $description = $request->input('description');
        $onCode = $request->input('onCode');
        $offCode = $request->input('offCode');
        $pulseLength = $request->input('pulseLength');
        $type = 1;

        $currentUserId = $this->currentUser()->id;
        $newDeviceId = $this->deviceModel->add($name, $description, $type, $currentUserId)->id;
        $this->rfDeviceModel->add($onCode, $offCode, $pulseLength, $newDeviceId);

        return redirect()->route('devices');
    }

    public function delete($deviceId)
    {
        $doesUserOwnDevice = $this->currentUser()->doesUserOwnDevice($deviceId);

        if ($doesUserOwnDevice) {
            $this->deviceModel->destroy($deviceId);
        }

        return redirect()->route('devices');
    }

    private function currentUser()
    {
        $userId = session(env('SESSION_USER_ID'));
        $currentUser = $this->userModel->where('user_id', $userId)->first();

        return $currentUser;
    }
}
