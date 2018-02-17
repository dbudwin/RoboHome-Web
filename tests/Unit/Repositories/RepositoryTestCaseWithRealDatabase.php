<?php

namespace Tests\Unit\Repositories;

use App\Http\Globals\DeviceTypes;
use App\User;
use Tests\Unit\Model\TestCaseWithRealDatabase;

class RepositoryTestCaseWithRealDatabase extends TestCaseWithRealDatabase
{
    protected function createUser(): User
    {
        return factory(User::class)->create();
    }

    protected function createDeviceProperties(): array
    {
        $properties = [
            'name' => self::$faker->word(),
            'description' => self::$faker->sentence(),
            'device_type_id' => DeviceTypes::RF_DEVICE,
            'on_code' => self::$faker->randomNumber(),
            'off_code' => self::$faker->randomNumber(),
            'pulse_length' => self::$faker->randomNumber()
        ];

        return $properties;
    }

    protected function assertNotEqualOrEmpty(string $expected, string $actual): void
    {
        $this->assertNotEmpty($expected);
        $this->assertNotEmpty($actual);
        $this->assertNotEquals($expected, $actual);
    }
}
