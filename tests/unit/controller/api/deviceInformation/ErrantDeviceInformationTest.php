<?php

namespace Tests\Unit\Controller\Api\DeviceInformation;

use App\Http\Controllers\API\DeviceInformation\ErrantDeviceInformation;
use Tests\TestCase;

class ErrantDeviceInformationTest extends TestCase
{
    public function testInfo_GivenRandomValues_Returns400(): void
    {
        $errantDeviceInformation = new ErrantDeviceInformation();
        $response = $errantDeviceInformation->info(self::$faker->randomDigit(), self::$faker->word());

        $this->assertEquals($response->status(), 400);
    }
}
