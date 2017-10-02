<?php

namespace App\Http\Controllers\Common;

use App\User;

trait DeviceOwner
{

    private function getCurrentUser(int $userId = null): ?User
    {
        $userId = is_null($userId) ? session(env('SESSION_USER_ID')) : $userId;
        return $this->userModel->where('user_id', $userId)->first();
    }

    private function isDeviceOwner(User $user, int $deviceId): bool
    {
        return $user->doesUserOwnDevice($deviceId);
    }

}