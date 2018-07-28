<?php

namespace Tests\Unit\Controller\API;

use App\User;
use Laravel\Passport\Passport;
use Tests\Unit\Controller\Common\ControllerTestCase;

class UsersControllerTest extends ControllerTestCase
{
    public function testPublicId_GivenUserWithRandomScope_Returns400(): void
    {
        Passport::actingAs(factory(User::class)->make(), [self::$faker->word()]);

        $response = $this->getJson('/api/users/publicId');

        $response->assertStatus(400);
        $response->assertExactJson(['error' => 'Missing scope']);
    }

    public function testPublicId_GivenUserWithScope_Returns200(): void
    {
        $user = factory(User::class)->make();

        Passport::actingAs($user, ['info']);

        $response = $this->getJson('/api/users/publicId');

        $response->assertStatus(200);
        $response->assertExactJson(['public_id' => $user->public_id]);
    }
}
