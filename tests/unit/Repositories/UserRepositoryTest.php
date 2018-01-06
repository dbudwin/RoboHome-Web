<?php

namespace Tests\Unit\Repositories;

use App\Repositories\IUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepositoryTest extends RepositoryTestCaseWithRealDatabase
{
    private $userRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->app->make(IUserRepository::class);
    }

    public function testGet_GivenUserDoesNotExist_ThrowsModelNotFoundException(): void
    {
        $nonexistentUserId = self::$faker->randomNumber();

        $this->expectException(ModelNotFoundException::class);

        $this->userRepository->get($nonexistentUserId);
    }

    public function testGet_GivenUserExists_ReturnsUser(): void
    {
        $user = $this->createUser();

        $retrievedUser = $this->userRepository->get($user->id);

        $this->assertTrue($retrievedUser->is($user));
    }
}
