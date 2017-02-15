<?php

namespace Tests\Unit\Controller\Web;

use App\Http\Wrappers\ICurlRequest;
use App\User;
use Mockery;
use Tests\Unit\Controller\Common\ControllerTestCase;

class LoginControllerTest extends ControllerTestCase
{
    public function testIndex_GivenUserNotLoggedIn_ResponseOk()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testIndex_GivenUserNotLoggedIn_ViewContainsExpectedText()
    {
        $response = $this->get('/');

        $response->assertSee('Welcome to RoboHome');
    }

    public function testIndex_GivenUserNotLoggedIn_SessionVariableNotSet()
    {
        $response = $this->get('/');

        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }

    public function testIndex_GivenUserLoggedIn_RedirectedToDevices()
    {
        $response = $this->withSession([env('SESSION_USER_ID') => self::$faker->text])->call('GET', '/');
        
        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testLogin_GivenAccessTokenNotSet_Returns401()
    {
        $response = $this->get('/login', ['access_token' => '']);

        $response->assertStatus(401);
    }

    public function testLogin_GivenBadAccessToken_Returns401()
    {
        $this->givenCurlRequests(self::$faker->uuid(), 1, 1, 0, 1);

        $response = $this->call('GET', '/login', ['access_token' => self::$faker->uuid]);

        $response->assertStatus(401);
    }

    public function testLogin_GivenUserLoggedIn_RedirectToDevices()
    {
        $this->givenCurlRequests(env('AMAZON_TOKEN'), 2, 3, 1, 2);
        $this->givenSingleUserExists();

        $response = $this->call('GET', '/login', ['access_token' => self::$faker->uuid]);

        $this->assertRedirectedToRouteWith302($response, '/devices');
    }

    public function testLogout_GivenUserNotLoggedIn_RedirectToIndex()
    {
        $response = $this->get('/logout');

        $this->assertRedirectedToRouteWith302($response, '/');
    }

    public function testLogout_GivenUserNotLoggedIn_SessionCleared()
    {
        $response = $this->get('/logout');

        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }

    public function testLogout_GivenUserLoggedIn_RedirectToIndex()
    {
        $response = $this->withSession([env('SESSION_USER_ID') => self::$faker->text])->get('/logout');

        $this->assertRedirectedToRouteWith302($response, '/');
    }

    public function testLogout_GivenUserLoggedIn_SessionCleared()
    {
        $response = $this->withSession([env('SESSION_USER_ID') => self::$faker->text])->get('/logout');

        $response->assertSessionMissing(env('SESSION_USER_ID'));
    }

    private function givenCurlRequests($token, $timesInitCalled, $timesSetOptionCalled, $timesSecondExecuteIsCalled, $timesClosedIsCalled)
    {
        $userId = self::$faker->uuid();

        $accessTokenResultJson =
        '{
            "aud": "' . $token . '",
            "user_id": "' . $userId . '",
            "iss": "' . self::$faker->url() . '",
            "exp": ' . self::$faker->randomNumber() . ',
            "app_id": "amzn1.application.' . dechex(self::$faker->randomNumber()) . '",
            "iat": ' . self::$faker->randomNumber() . '
        }';

        $userProfileResultJson =
        '{
            "user_id": "' . $userId . '",
            "name": "' . self::$faker->name() . '",
            "email": "' . self::$faker->email() . '"
        }';

        $mockCurlRequest = Mockery::mock(ICurlRequest::class);
        $mockCurlRequest
            ->shouldReceive('init')->withAnyArgs()->times($timesInitCalled)
            ->shouldReceive('setOption')->withAnyArgs()->times($timesSetOptionCalled)
            ->shouldReceive('execute')->once()->andReturn($accessTokenResultJson)
            ->shouldReceive('execute')->times($timesSecondExecuteIsCalled)->andReturn($userProfileResultJson)
            ->shouldReceive('close')->times($timesClosedIsCalled);
        $this->app->instance(ICurlRequest::class, $mockCurlRequest);
    }

    private function givenSingleUserExists()
    {
        $mockUserTable = Mockery::mock(User::class)->makePartial();
        $mockUserTable
            ->shouldReceive('where')->withAnyArgs()->once()->andReturn(Mockery::self())
            ->shouldReceive('first')->once()
            ->shouldReceive('add')->withAnyArgs()->once()->andReturn(Mockery::self());
        $this->app->instance(User::class, $mockUserTable);
    }
}
