<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Webpatser\Uuid\Uuid;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public static function boot(): void
    {
        parent::boot();

        self::creating(function (User $user) {
            $user->public_id = Uuid::generate(4);
        });
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function ownsDevice(int $deviceId): bool
    {
        return $this->devices->contains($deviceId);
    }
}
