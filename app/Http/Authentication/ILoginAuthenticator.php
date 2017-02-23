<?php

namespace App\Http\Authentication;

use Illuminate\Http\Request;

interface ILoginAuthenticator
{
    public function processLoginRequest(Request $request);
}
