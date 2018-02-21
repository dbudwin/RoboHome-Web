<?php

namespace Tests\Unit\Controller\API;

use App\User;
use Laravel\Passport\Passport;
use Tests\Unit\Controller\Common\ControllerTestCase;

class SettingsControllerTest extends ControllerTestCase
{
    public function testMqtt_GivenUserNotLoggedIn_Returns401(): void
    {
        $response = $this->postJson('/api/settings/mqtt');

        $response->assertStatus(401);
    }

    public function testMqtt_GivenUserWithRandomScope_Returns400(): void
    {
        Passport::actingAs(factory(User::class)->make(), [self::$faker->word()]);

        $response = $this->postJson('/api/settings/mqtt');

        $response->assertStatus(400);
    }

    public function testMqtt_GivenUserWithInfoScope_ReturnsMqttSettings(): void
    {
        $server = self::$faker->domainName();
        $tlsPort = self::$faker->randomNumber();
        $user = self::$faker->email();
        $password = self::$faker->password();

        putenv("MQTT_SERVER=$server");
        putenv("MQTT_TLS_PORT=$tlsPort");
        putenv("MQTT_USER=$user");
        putenv("MQTT_PASSWORD=$password");

        Passport::actingAs(factory(User::class)->make(), ['info']);

        $response = $this->postJson('/api/settings/mqtt');

        $response->assertStatus(200);
        $response->assertExactJson([
            'mqtt' => [
                'server' => $server,
                'tlsPort' => strval($tlsPort),
                'user' => $user,
                'password' => $password
            ]
        ]);
    }
}
