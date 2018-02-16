<?php

namespace App\Exceptions;

use App\Traits\RestExceptionHandler;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    use RestExceptionHandler;

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Exception $exception): void
    {
        parent::report($exception);
    }

    public function render($request, Exception $exception): Response
    {
        if (!$this->isApiCall($request)) {
            return parent::render($request, $exception);
        }

        return $this->jsonResponseForException($exception);
    }

    private function isApiCall(Request $request): bool
    {
        return strpos($request->getUri(), '/api/') !== false;
    }
}
