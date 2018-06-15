<?php

namespace Tests;

use App\RFDevice;
use Faker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\JsonResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $faker;

    public static function setUpBeforeClass(): void
    {
        self::$faker = Faker\Factory::create();
    }

    protected function assertJsonResponse(JsonResponse $response, string $expectedJson, int $expectedStatusCode): void
    {
        $this->assertEquals($response->getStatusCode(), $expectedStatusCode);
        $this->assertJsonStringEqualsJsonString(json_encode($response->getData()), $expectedJson);
    }

    protected function makeRFDevice(int $onCode, int $offCode, int $pulseLength): RFDevice
    {
        return factory(RFDevice::class)->make([
            'device_id' => self::$faker->randomNumber(),
            'on_code' => $onCode,
            'off_code' => $offCode,
            'pulse_length' => $pulseLength,
        ]);
    }
}
