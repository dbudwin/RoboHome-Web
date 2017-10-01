<?php

namespace Tests\Unit\MQTT;

use App\Http\MQTT\MessagePublisher;
use LibMQTT\Client;
use Mockery;
use Tests\TestCase;

class MessagePublisherTest extends TestCase
{
    private $userId;
    private $deviceId;
    private $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->userId = self::$faker->uuid();
        $this->deviceId = self::$faker->randomDigit();
        $this->action = self::$faker->word();
    }

    public function testPublish_GivenValidConnection_ReturnsTrue(): void
    {
        $topic = "RoboHome/$this->userId/$this->deviceId";

        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('connect')->once()->andReturn(true);
        $mockClient->shouldReceive('publish')->withArgs([$topic, $this->action, 0])->once();
        $mockClient->shouldReceive('close')->once();

        $messagePublisher = new MessagePublisher($mockClient);

        $result = $messagePublisher->publish($this->userId, $this->action, $this->deviceId);

        $this->assertTrue($result);
    }

    public function testPublish_GivenValidConnection_ReturnsFalse(): void
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('connect')->once()->andReturn(false);
        $mockClient->shouldNotReceive('publish');
        $mockClient->shouldNotReceive('close');

        $messagePublisher = new MessagePublisher($mockClient);

        $result = $messagePublisher->publish($this->userId, $this->action, $this->deviceId);

        $this->assertFalse($result);
    }
}
