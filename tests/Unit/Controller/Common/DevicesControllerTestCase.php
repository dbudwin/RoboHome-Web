<?php

namespace Tests\Unit\Controller\Common;

use App\Http\MQTT\MessagePublisher;
use Mockery;

class DevicesControllerTestCase extends ControllerTestCase
{
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
