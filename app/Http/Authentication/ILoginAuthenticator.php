<?php

namespace App\Http\Authentication;

use App\User;
use Illuminate\Http\Request;

interface ILoginAuthenticator
{
    public function processLoginRequest(Request $request): ?User;

    public function processApiLoginRequest(Request $request): ?User;
}
