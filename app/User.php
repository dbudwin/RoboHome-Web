<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'user_id'];
    protected $table = 'users';

    public function add($name, $email, $userId)
    {
        $this->name = $name;
        $this->email = $email;
        $this->user_id = $userId;
        $this->save();

        return $this;
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function doesUserOwnDevice($deviceId)
    {
        return $this->devices->contains($deviceId);
    }
}
