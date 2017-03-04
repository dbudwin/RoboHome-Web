<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LibMQTT\Client;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('App\Http\Authentication\ILoginAuthenticator', 'App\Http\Authentication\AmazonLoginAuthenticator');
        $this->app->bind('App\Http\Wrappers\ICurlRequest', 'App\Http\Wrappers\CurlRequest');
        $this->app->bind('LibMQTT\Client', function () {
            $maxClientIdLength = 23;
            $clientId = substr(str_shuffle(MD5(microtime())), 0, $maxClientIdLength);
            $client = new Client(env('MQTT_SERVER'), env('MQTT_PORT'), $clientId);
            $client->setAuthDetails(env('MQTT_USER'), env('MQTT_PASSWORD'));

            return $client;
        });
    }
}
