<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

class Device extends Model
{
    protected $fillable = ['name', 'description'];
    protected $table = 'devices';

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function (Device $device) {
            $device->specificDevice()->delete();
        });

        self::creating(function (Device $device) {
            $device->public_id = Uuid::generate(4);
        });
    }

    public function htmlDataAttributesForSpecificDevice(): array
    {
        $specificDevice = $this->specificDevice();

        $properties = $specificDevice->getFillable();

        $htmlDataAttributesForSpecificDeviceProperties = [];

        foreach ($properties as $property) {
            $propertyName = str_replace('_', '-', $property);
            $propertyValue = $specificDevice->$property;
            $htmlDataAttributesForSpecificDeviceProperties[] = 'data-device-' . $propertyName . '=' . $propertyValue;
        }

        return $htmlDataAttributesForSpecificDeviceProperties;
    }

    private function specificDevice(): Model
    {
        $deviceTypeId = $this->device_type_id;
        $deviceType = DeviceType::find($deviceTypeId)->type;
        $deviceTypeClassName = 'App\\' . $deviceType . 'Device';
        $specificDevice = $this->hasOne($deviceTypeClassName)->first();

        return $specificDevice;
    }
}
