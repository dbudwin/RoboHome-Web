<?php

namespace App\Http\MQTT;

use LibMQTT\Client;
use Webpatser\Uuid\Uuid;

class MessagePublisher
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function publish(Uuid $publicUserId, string $action, int $deviceId): bool
    {
        if (!$this->client->connect()) {
            return false;
        }

        $published = $this->client->publish("RoboHome/$publicUserId->string/$deviceId", $action, 0);

        $this->client->close();

        return $published;
    }
}
