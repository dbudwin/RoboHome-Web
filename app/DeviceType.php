<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    protected $guarded = ['*'];
    protected $table = 'device_types';
}
