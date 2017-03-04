<?php

namespace App\Http\MQTT;

use LibMQTT\Client;

class MessagePublisher
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function publish($userId, $deviceId, $action)
    {
        if (!$this->client->connect()) {
            return false;
        }

        $this->client->publish("RoboHome/$userId/$deviceId", $action, 0);
        $this->client->close();

        return true;
    }
}
