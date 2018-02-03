<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use LibMQTT\Client;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }

    public function register(): void
    {
        $this->app->bind(Client::class, function () {
            $maxClientIdLength = 23;
            $clientId = substr(str_shuffle(MD5(microtime())), 0, $maxClientIdLength);
            $client = new Client(env('MQTT_SERVER'), env('MQTT_PORT'), $clientId);
            $client->setAuthDetails(env('MQTT_USER'), env('MQTT_PASSWORD'));

            return $client;
        });
    }
}
