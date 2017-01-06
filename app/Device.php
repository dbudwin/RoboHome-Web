<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['name', 'description', 'type'];
    protected $table = 'devices';
    
    public function add($name, $description, $type)
    {
        $this->name = $name;
        $this->description = $description;
        $this->device_type_id = $type;
        $this->save();

        return $this;
    }

    public function rfDevice()
    {
        return $this->hasOne(RFDevice::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($device) {
            $device->rfDevice()->delete();
        });
    }
}
