<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = ['name', 'email', 'user_id'];
    protected $table = 'users';

    public function add(string $name, string $email, string $userId) : User
    {
        $this->name = $name;
        $this->email = $email;
        $this->user_id = $userId;
        $this->save();

        return $this;
    }

    public function devices() : HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function doesUserOwnDevice($deviceId) : bool
    {
        return $this->devices->contains($deviceId);
    }
}
