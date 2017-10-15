<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function doesUserOwnDevice($deviceId): bool
    {
        return $this->devices->contains($deviceId);
    }
}
