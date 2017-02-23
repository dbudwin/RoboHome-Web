<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('App\Http\Authentication\ILoginAuthenticator', 'App\Http\Authentication\AmazonLoginAuthenticator');
        $this->app->bind('App\Http\Wrappers\ICurlRequest', 'App\Http\Wrappers\CurlRequest');
    }
}
