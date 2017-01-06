<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RFDevice extends Model
{
    protected $fillable = ['on_code', 'off_code', 'pulse_length', 'device_id'];
    protected $table = 'rf_devices';

    public function add($onCode, $offCode, $pulseLength, $deviceId)
    {
        $this->on_code = $onCode;
        $this->off_code = $offCode;
        $this->pulse_length = $pulseLength;
        $this->device_id = $deviceId;
        $this->save();

        return $this;
    }
}
