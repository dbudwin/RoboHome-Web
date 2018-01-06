<?php

namespace App\Repositories;

use App\User;

interface IUserRepository
{
    public function get(int $id): User;
}
