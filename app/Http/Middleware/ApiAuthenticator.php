<?php

namespace App\Http\Middleware;

use App\Http\Authentication\ILoginAuthenticator;
use Closure;

class ApiAuthenticator
{
    private $loginAuthenticator;

    public function __construct(ILoginAuthenticator $loginAuthenticator)
    {
        $this->loginAuthenticator = $loginAuthenticator;
    }

    public function handle($request, Closure $next)
    {
        $user = $this->loginAuthenticator->processApiLoginRequest($request);

        if ($user === null) {
            abort(401, 'Unauthorized');
        }

        $request->attributes->add(['currentUserId' => $user->user_id]);

        return $next($request);
    }
}
