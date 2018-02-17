<?php

namespace App\Traits;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait RestExceptionHandler
{
    protected function jsonResponseForException(Exception $exception): JsonResponse
    {
        switch (true) {
            case $this->isAuthenticationException($exception):
                return $this->notAuthenticated();
            case $this->isModelNotFoundException($exception):
                return $this->modelNotFound();
            default:
                return $this->badRequest();
        }
    }

    private function notAuthenticated(): JsonResponse
    {
        return $this->jsonResponse(['error' => 'User not authenticated'], 401);
    }

    private function modelNotFound(): JsonResponse
    {
        return $this->jsonResponse(['error' => 'Record not found'], 404);
    }

    private function badRequest(): JsonResponse
    {
        return $this->jsonResponse(['error' => 'Bad request'], 400);
    }

    private function isAuthenticationException(Exception $exception): bool
    {
        return $exception instanceof AuthenticationException;
    }

    private function isModelNotFoundException(Exception $exception): bool
    {
        return $exception instanceof ModelNotFoundException;
    }

    private function jsonResponse(array $payload, int $statusCode): JsonResponse
    {
        return response()->json($payload, $statusCode);
    }
}
