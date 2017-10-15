<?php

namespace Tests\Unit\Controller\Common;

use App\Http\MQTT\MessagePublisher;
use App\User;
use Mockery;
use Mockery\MockInterface;

class DevicesControllerTestCase extends ControllerTestCase
{
    protected function givenSingleUserExists(): User
    {
        $user = $this->createUser();

        $mockUserTable = Mockery::mock(User::class);
        $mockUserTable
            ->shouldReceive('where')->with('id', $user->id)->atMost()->once()->andReturn(Mockery::self())
            ->shouldReceive('first')->atMost()->once()->andReturn($user);

        $this->app->instance(User::class, $mockUserTable);

        return $user;
    }

    protected function createUser(): User
    {
        $user = factory(User::class)->make([
            'id' => self::$faker->randomNumber()
        ]);

        return $user;
    }

    protected function mockMessagePublisher(int $timesPublishIsCalled): void
    {
        $mockMessagePublisher = Mockery::mock(MessagePublisher::class);
        $mockMessagePublisher->shouldReceive('publish')->withAnyArgs()->times($timesPublishIsCalled)->andReturn(true);

        $this->app->instance(MessagePublisher::class, $mockMessagePublisher);
    }

    protected function mockUserOwnsDevice(int $deviceId, bool $doesUserOwnDevice): MockInterface
    {
        $mockUserRecord = Mockery::mock(User::class);
        $mockUserRecord->shouldReceive('doesUserOwnDevice')->with($deviceId)->once()->andReturn($doesUserOwnDevice);

        return $mockUserRecord;
    }
}
