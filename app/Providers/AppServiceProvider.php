<?php

namespace App\Providers;

use App\Http\Authentication\AmazonLoginAuthenticator;
use App\Http\Authentication\ILoginAuthenticator;
use App\Http\Wrappers\CurlRequest;
use App\Http\Wrappers\ICurlRequest;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use LibMQTT\Client;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ILoginAuthenticator::class, AmazonLoginAuthenticator::class);
        $this->app->bind(ICurlRequest::class, CurlRequest::class);
        $this->app->bind(Client::class, function () {
            $maxClientIdLength = 23;
            $clientId = substr(str_shuffle(MD5(microtime())), 0, $maxClientIdLength);
            $client = new Client(env('MQTT_SERVER'), env('MQTT_PORT'), $clientId);
            $client->setAuthDetails(env('MQTT_USER'), env('MQTT_PASSWORD'));
            if (env('MQTT_TLS', false)) {
                $client->setCryptoProtocol("tls");
            }

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
