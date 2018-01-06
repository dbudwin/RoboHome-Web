<?php

namespace App\Repositories;

use App\User;

class UserRepository implements IUserRepository
{
    public function get(int $id): User
    {
        return User::findOrFail($id);
    }
}
