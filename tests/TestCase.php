<?php

namespace Tests;

use Faker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $faker;

    public static function setUpBeforeClass()
    {
        self::$faker = Faker\Factory::create();
    }
}
