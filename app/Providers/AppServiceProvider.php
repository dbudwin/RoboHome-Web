<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use LibMQTT\Client;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Client::class, function () {
            $maxClientIdLength = 23;
            $clientId = substr(str_shuffle(MD5(microtime())), 0, $maxClientIdLength);
            $client = new Client(env('MQTT_SERVER'), env('MQTT_PORT'), $clientId);
            $client->setAuthDetails(env('MQTT_USER'), env('MQTT_PASSWORD'));

            return $client;
        });

        $this->registerDuskServiceProvider();
    }

    private function registerDuskServiceProvider(): void
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
