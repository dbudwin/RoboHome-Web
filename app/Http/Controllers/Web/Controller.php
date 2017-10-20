<?php

namespace App\Http\Controllers\Web;

use App\User;
use Illuminate\View\View;
use App\Http\Controllers\Common\Controller as CommonController;

abstract class Controller extends CommonController
{
    public function devices(): View
    {
        $currentUser = $this->currentUser();

        return view('devices', [
            'name' => $currentUser->name,
            'devices' => $currentUser->devices
        ]);
    }

    private function currentUser(): User
    {
        $userId = session(env('SESSION_USER_ID'));
        return User::where('user_id', $userId)->firstOrFail();
    }
}
