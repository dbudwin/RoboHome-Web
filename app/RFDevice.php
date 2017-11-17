<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RFDevice extends Model
{
    protected $fillable = ['on_code', 'off_code', 'pulse_length'];
    protected $table = 'rf_devices';
}
