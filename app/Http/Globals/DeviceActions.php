<?php

namespace App\Http\Globals;

abstract class DeviceActions
{
    const TURN_ON = 'turnOn';
    const TURN_OFF = 'turnOff';

    public static function actionToConfirmationName(string $action): string
    {
        switch ($action) {
            case strtolower(DeviceActions::TURN_ON):
                return 'TurnOnConfirmation';
            case strtolower(DeviceActions::TURN_OFF):
                return 'TurnOffConfirmation';
        }
    }
}
