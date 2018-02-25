<?php

namespace Tests\Unit\Controller\Common;

use Illuminate\Foundation\Testing\TestResponse;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class ControllerTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function assertRedirectedToRouteWith302(TestResponse $response, string $route): void
    {
        $response->assertStatus(302);
        $response->assertRedirect($route);
    }
}
