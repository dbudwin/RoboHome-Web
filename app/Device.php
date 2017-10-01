<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Device extends Model
{
    protected $fillable = ['name', 'description'];
    protected $table = 'devices';

    public function add(string $name, string $description, int $userId, int $type): Device
    {
        $this->name = $name;
        $this->description = $description;
        $this->user_id = $userId;
        $this->device_type_id = $type;
        $this->save();

        return $this;
    }

    public function htmlDataAttributesForSpecificDeviceProperties(): array
    {
        $specificDevice = $this->specificDevice()->first();

        $properties = $specificDevice->getFillable();

        $htmlDataAttributesForSpecificDeviceProperties = [];

        foreach ($properties as $property) {
            $propertyName = str_replace('_', '-', $property);
            $propertyValue = $specificDevice->$property;
            $htmlDataAttributesForSpecificDeviceProperties[] = 'data-device-' . $propertyName . '=' . $propertyValue;
        }

        return $htmlDataAttributesForSpecificDeviceProperties;
    }

    public function specificDevice(): HasOne
    {
        $deviceTypeId = $this->device_type_id;
        $deviceType = DeviceType::where('id', $deviceTypeId)->first()->type;
        $deviceTypeClassName = 'App\\' . $deviceType . 'Device';
        $specificDevice = $this->hasOne($deviceTypeClassName);

        return $specificDevice;
    }

    public static function boot(): void
    {
        parent::boot();

        static::deleting(function ($device) {
            $device->specificDevice()->delete();
        });
    }
}
