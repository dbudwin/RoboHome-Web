<?php

namespace Tests\Unit\Controller;

use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;

class ControllerTestCase extends TestCase
{
    protected function assertRedirectedToRouteWith302(TestResponse $response, $route)
    {
        $response->assertStatus(302);
        $response->assertRedirect($route);
    }
}
