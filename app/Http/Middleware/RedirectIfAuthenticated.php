<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next)
    {
        if ($request->session()->has(env('SESSION_USER_ID'))) {
            return $next($request);
        }

        return redirect()->route('index');
    }
}
