<?php

namespace Tests\Unit\Traits;

use App\Traits\RestExceptionHandler;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class RestExceptionHandlerTest extends TestCase
{
    private $mockRestExceptionHandler;

    public function setUp()
    {
        parent::setUp();

        $this->mockRestExceptionHandler = $this->getMockForTrait(RestExceptionHandler::class);
    }

    public function testJsonResponseForException_GivenAuthenticationException_Returns401WithError()
    {
        $response = $this->mockRestExceptionHandler->jsonResponseForException(new AuthenticationException());

        $this->assertJsonResponse($response, json_encode(['error' => 'User not authenticated']), 401);
    }

    public function testJsonResponseForException_GivenModelNotFoundException_Returns404WithError()
    {
        $response = $this->mockRestExceptionHandler->jsonResponseForException(new ModelNotFoundException());

        $this->assertJsonResponse($response, json_encode(['error' => 'Record not found']), 404);
    }

    public function testJsonResponseForException_GivenException_Returns400WithError()
    {
        $response = $this->mockRestExceptionHandler->jsonResponseForException(new Exception());

        $this->assertJsonResponse($response, json_encode(['error' => 'Bad request']), 400);
    }

    private function assertJsonResponse(JsonResponse $response, string $expectedJson, int $expectedStatusCode): void
    {
        $this->assertEquals($response->getStatusCode(), $expectedStatusCode);
        $this->assertJsonStringEqualsJsonString(json_encode($response->getData()), $expectedJson);
    }
}
