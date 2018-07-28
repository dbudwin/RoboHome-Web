<?php

namespace Tests\Unit\Controller\API;

use App\User;
use Laravel\Passport\Passport;
use Tests\Unit\Controller\Common\ControllerTestCase;

class SettingsControllerTest extends ControllerTestCase
{
    public function testMqtt_GivenUserNotLoggedIn_Returns401(): void
    {
        $response = $this->getJson('/api/settings/mqtt');

        $response->assertStatus(401);
        $response->assertExactJson(['error' => 'User not authenticated']);
    }

    public function testMqtt_GivenUserWithRandomScope_Returns400(): void
    {
        Passport::actingAs(factory(User::class)->make(), [self::$faker->word()]);

        $response = $this->getJson('/api/settings/mqtt');

        $response->assertStatus(400);
        $response->assertExactJson(['error' => 'Missing scope']);
    }

    public function testMqtt_GivenUserWithInfoScope_ReturnsMqttSettings(): void
    {
        $user = factory(User::class)->make();
        $publicUserId = $user->public_id;

        $mqttServer = self::$faker->domainName();
        $mqttTlsPort = self::$faker->randomNumber();
        $mqttUser = self::$faker->email();
        $mqttPassword = self::$faker->password();
        $mqttTopic = "RoboHome/$publicUserId/+";

        putenv("MQTT_SERVER=$mqttServer");
        putenv("MQTT_TLS_PORT=$mqttTlsPort");
        putenv("MQTT_USER=$mqttUser");
        putenv("MQTT_PASSWORD=$mqttPassword");

        Passport::actingAs($user, ['info']);

        $response = $this->getJson('/api/settings/mqtt');

        $response->assertStatus(200);
        $response->assertExactJson([
            'mqtt' => [
                'server' => $mqttServer,
                'tlsPort' => strval($mqttTlsPort),
                'user' => $mqttUser,
                'password' => $mqttPassword,
                'topic' => $mqttTopic
            ]
        ]);
    }
}
