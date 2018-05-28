<?php

namespace App\Http\MQTT;

use Illuminate\Support\Facades\Validator;
use LibMQTT\Client;
use Webpatser\Uuid\Uuid;

class MessagePublisher
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function publish(string $action, Uuid $publicUserId, Uuid $publicDeviceId): bool
    {
        if (!$this->client->connect()) {
            return false;
        }

        $published = $this->client->publish("RoboHome/$publicUserId->string/$publicDeviceId->string", $action, 0);

        $this->client->close();

        return $published;
    }
}
