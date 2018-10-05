<?php

namespace App\Http\Globals;

abstract class DeviceActions
{
    const TURN_ON = 'turnOn';
    const TURN_OFF = 'turnOff';

    public static function actionToDirectiveName(string $action): string
    {
        switch (strtolower($action)) {
            case strtolower(DeviceActions::TURN_ON):
                return 'ON';
            case strtolower(DeviceActions::TURN_OFF):
                return 'OFF';
        }
    }
}
