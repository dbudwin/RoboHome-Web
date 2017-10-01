<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RFDevice extends Model
{
    protected $fillable = ['on_code', 'off_code', 'pulse_length'];
    protected $table = 'rf_devices';

    public function add(int $onCode, int $offCode, int $pulseLength, int $deviceId): RFDevice
    {
        $this->on_code = $onCode;
        $this->off_code = $offCode;
        $this->pulse_length = $pulseLength;
        $this->device_id = $deviceId;
        $this->save();

        return $this;
    }
}
