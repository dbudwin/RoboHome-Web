<?php

namespace App\Http\Middleware;

use App\Http\Authentication\ILoginAuthenticator;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticator
{
    private $loginAuthenticator;

    public function __construct(ILoginAuthenticator $loginAuthenticator)
    {
        $this->loginAuthenticator = $loginAuthenticator;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $this->loginAuthenticator->processApiLoginRequest($request);

        if ($user === null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->attributes->add(['currentUserId' => $user->user_id]);

        return $next($request);
    }
}
