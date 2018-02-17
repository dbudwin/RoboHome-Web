<?php

namespace Tests\Unit\Controller\Common;

use App\Http\MQTT\MessagePublisher;
use App\User;
use Mockery;

class DevicesControllerTestCase extends ControllerTestCase
{
    protected function createUser(): User
    {
        $user = factory(User::class)->make([
            'id' => self::$faker->randomNumber()
        ]);

        return $user;
    }

    protected function mockMessagePublisher(int $timesPublishIsCalled, bool $messagePublishedSuccessfully = true): void
    {
        $mockMessagePublisher = Mockery::mock(MessagePublisher::class);
        $mockMessagePublisher
            ->shouldReceive('publish')
            ->withAnyArgs()
            ->times($timesPublishIsCalled)
            ->andReturn($messagePublishedSuccessfully);

        $this->app->instance(MessagePublisher::class, $mockMessagePublisher);
    }
}
