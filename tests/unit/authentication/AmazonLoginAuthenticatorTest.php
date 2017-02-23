<?php

namespace Tests\Unit\Authentication;

use App\Http\Authentication\AmazonLoginAuthenticator;
use App\Http\Wrappers\ICurlRequest;
use App\User;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class AmazonLoginAuthenticatorTest extends TestCase
{
    private $amazonLoginAuthenticator;
    private $mockCurlRequest;
    private $user;

    public function setUp()
    {
        parent::setUp();

        $this->mockCurlRequest = Mockery::mock(ICurlRequest::class);
        $this->user = $this->createUser();

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->withAnyArgs()->andReturn(Mockery::self())
            ->shouldReceive('first')->withAnyArgs()->andReturn($this->user);
        $this->app->instance(User::class, $mockUserTable);

        $this->amazonLoginAuthenticator = new AmazonLoginAuthenticator($this->mockCurlRequest, $mockUserTable);
    }

    public function testProcessLogin_GivenValidCurlRequests_ReturnsExistingUser()
    {
        $this->givenValidCurlRequests(env('AMAZON_TOKEN'), 2, 3, 1, 2);

        $result = $this->callProcessLoginRequest();

        $this->assertEquals($this->user, $result);
    }

    public function testProcessLogin_GivenValidCurlRequests_ReturnsNewUser()
    {
        $user = $this->createUser();

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->withAnyArgs()->once()->andReturn(Mockery::self())
            ->shouldReceive('first')->once()
            ->shouldReceive('add')->withAnyArgs()->once()->andReturn($user);
        $this->app->instance(User::class, $mockUserTable);

        $this->amazonLoginAuthenticator = new AmazonLoginAuthenticator($this->mockCurlRequest, $mockUserTable);

        $this->givenValidCurlRequests(env('AMAZON_TOKEN'), 2, 3, 1, 2);

        $result = $this->callProcessLoginRequest();

        $this->assertEquals($user, $result);
    }

    public function testProcessLogin_GivenValidCurlRequestsWithNonmatchingAccessToken_ReturnsNull()
    {
        $this->givenValidCurlRequests(self::$faker->uuid(), 1, 1, 0, 1);

        $result = $this->callProcessLoginRequest();

        $this->assertNull($result);
    }

    public function testProcessLogin_GivenValidCurlRequestsNoTokenReturnedFromAmazon_ReturnsNull()
    {
        $this->givenBadAccessTokenCurlRequests(env('AMAZON_TOKEN'), 1, 1, 1);

        $result = $this->callProcessLoginRequest();

        $this->assertNull($result);
    }

    private function createUser()
    {
        $user = new User();

        $user->id = self::$faker->randomDigit();
        $user->name = self::$faker->name();
        $user->email = self::$faker->email();
        $user->user_id = self::$faker->uuid();

        return $user;
    }

    private function callProcessLoginRequest()
    {
        $mockRequest = Mockery::mock(Request::class);
        $mockRequest->shouldReceive('query')->with('access_token')->once()->andReturn(self::$faker->uuid());

        $result = $this->amazonLoginAuthenticator->processLoginRequest($mockRequest);

        return $result;
    }

    private function givenValidCurlRequests($token, $timesInitCalled, $timesSetOptionCalled, $timesSecondExecuteIsCalled, $timesClosedIsCalled)
    {
        $userId = self::$faker->uuid;

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

        $this->mockCurlRequest
            ->shouldReceive('init')->withAnyArgs()->times($timesInitCalled)
            ->shouldReceive('setOption')->withAnyArgs()->times($timesSetOptionCalled)
            ->shouldReceive('execute')->once()->andReturn($accessTokenResultJson)
            ->shouldReceive('execute')->times($timesSecondExecuteIsCalled)->andReturn($userProfileResultJson)
            ->shouldReceive('close')->times($timesClosedIsCalled);
    }

    private function givenBadAccessTokenCurlRequests($curlReturnValue, $timesInitCalled, $timesSetOptionCalled, $timesClosedIsCalled)
    {
        $this->mockCurlRequest
            ->shouldReceive('init')->withAnyArgs()->times($timesInitCalled)
            ->shouldReceive('setOption')->withAnyArgs()->times($timesSetOptionCalled)
            ->shouldReceive('execute')->once()->andReturn($curlReturnValue)
            ->shouldReceive('close')->times($timesClosedIsCalled);
    }
}
