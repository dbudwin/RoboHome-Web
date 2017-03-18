<?php

namespace Tests\Unit\Controller\Api\DeviceInformation;

use App\Http\Controllers\API\DeviceInformation\ErrantDeviceInformation;
use Tests\TestCase;

class ErrantDeviceInformationTest extends TestCase
{
    public function testInfo()
    {
        $errantDeviceInformation = new ErrantDeviceInformation();
        $response = $errantDeviceInformation->info(\Mockery::any(), \Mockery::any());

        $this->assertEquals($response->status(), 400);
    }
}
