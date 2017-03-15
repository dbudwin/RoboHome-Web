<?php

namespace App\Http\Middleware;

use Closure;

class AddContentLengthHeader
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        mb_internal_encoding('UTF-8');

        $content = $response->getOriginalContent();
        $decodedJson = json_encode($content);
        $length = mb_strlen($decodedJson);

        $response->header('Content-Length', $length);

        return $response;
    }
}
