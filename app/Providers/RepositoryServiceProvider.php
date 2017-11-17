<?php

namespace App\Providers;

use App\Repositories\DeviceRepository;
use App\Repositories\IDeviceRepository;
use App\Repositories\IRFDeviceRepository;
use App\Repositories\RFDeviceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IDeviceRepository::class, DeviceRepository::class);
        $this->app->bind(IRFDeviceRepository::class, RFDeviceRepository::class);
    }
}
