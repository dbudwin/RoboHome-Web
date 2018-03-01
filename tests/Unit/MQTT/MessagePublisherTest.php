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

    public function testPublish_GivenValidConnection_WhenMessageSuccessfullyPublished_ReturnsTrue(): void
    {
        $mockClient = $this->mockClient(true);

        $messagePublisher = new MessagePublisher($mockClient);

        $result = $messagePublisher->publish($this->userId, $this->action, $this->deviceId);

        $this->assertTrue($result);
    }

    public function testPublish_GivenValidConnection_WhenMessageUnsuccessfullyPublished_ReturnsFalse(): void
    {
        $mockClient = $this->mockClient(false);

        $messagePublisher = new MessagePublisher($mockClient);

        $result = $messagePublisher->publish($this->userId, $this->action, $this->deviceId);

        $this->assertFalse($result);
    }

    public function testPublish_GivenInvalidConnection_ReturnsFalse(): void
    {
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('connect')->once()->andReturn(false);
        $mockClient->shouldNotReceive('publish');
        $mockClient->shouldNotReceive('close');

        $messagePublisher = new MessagePublisher($mockClient);

        $result = $messagePublisher->publish($this->userId, $this->action, $this->deviceId);

        $this->assertFalse($result);
    }

    private function mockClient(bool $publishedSuccessfully)
    {
        $topic = "RoboHome/$this->userId/$this->deviceId";

        $mockClient = Mockery::mock(Client::class);
        $mockClient
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('publish')->withArgs([$topic, $this->action, 0])->once()->andReturn($publishedSuccessfully)
            ->shouldReceive('close')->once();

        return $mockClient;
    }
}
