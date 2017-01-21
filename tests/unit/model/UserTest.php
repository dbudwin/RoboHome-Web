<?php

namespace Tests\Unit\Model;

use App\User;

class UserTest extends ModelTestCase
{
    public function testAdd_GivenUserAddedToDatabase_DatabaseOnlyHasOneUserRecord()
    {
        $user = new User;
        $name = self::$faker->name();
        $email = self::$faker->email();
        $userId = self::$faker->uuid();

        $user = $user->add($name, $email, $userId);

        $this->assertCount(1, User::all());
        $this->assertEquals($name, $user->name);
        $this->assertEquals($email, $user->email);
        $this->assertEquals($userId, $user->user_id);
    }
}
