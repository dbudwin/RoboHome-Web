<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfGuest
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has(env('SESSION_USER_ID'))) {
            return $next($request);
        }

        return redirect()->route('index');
    }
}
