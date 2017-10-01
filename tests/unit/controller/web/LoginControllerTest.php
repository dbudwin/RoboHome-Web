<?php

namespace Tests\Unit\Controller\Web;

use App\Http\Authentication\ILoginAuthenticator;
use App\User;
use Mockery;
use Tests\Unit\Controller\Common\ControllerTestCase;

class LoginControllerTest extends ControllerTestCase
{
    public function testIndex_GivenUserNotLoggedIn_ResponseOk(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testIndex_GivenUserNotLoggedIn_ViewContainsExpectedText(): void
    {
        $response = $this->get('/');

        $response->assertSee('Welcome to RoboHome');
    }

    public function testIndex_GivenUserNotLoggedIn_SessionVariableNotSet(): void
    {
        $response = $this->get('/');

        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }

    public function testIndex_GivenUserLoggedIn_RedirectedToDevices(): void
    {
        $response = $this->withSession([env('SESSION_USER_ID') => self::$faker->text()])->get('/');

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testLogin_GivenEmptyRequest_Returns401(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(401);
    }

    public function testLogin_GivenUserLoggedIn_RedirectToDevices(): void
    {
        $mockLoginAuthenticator = Mockery::mock(ILoginAuthenticator::class);
        $mockLoginAuthenticator->shouldReceive('processLoginRequest')->withAnyArgs()->once()->andReturn(new User());

        $this->app->instance(ILoginAuthenticator::class, $mockLoginAuthenticator);

        $response = $this->get('/login');

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testLogout_GivenUserNotLoggedIn_RedirectToIndex(): void
    {
        $response = $this->get('/logout');

        $this->assertRedirectedToRouteWith302($response, '/');
    }

    public function testLogout_GivenUserNotLoggedIn_SessionCleared(): void
    {
        $response = $this->get('/logout');

        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }

    public function testLogout_GivenUserLoggedIn_RedirectToIndex(): void
    {
        $response = $this->withSession([env('SESSION_USER_ID') => self::$faker->text()])->get('/logout');

        $this->assertRedirectedToRouteWith302($response, '/');
    }

    public function testLogout_GivenUserLoggedIn_SessionCleared(): void
    {
        $response = $this->withSession([env('SESSION_USER_ID') => self::$faker->text()])->get('/logout');

        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }
}
