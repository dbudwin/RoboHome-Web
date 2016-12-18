<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

abstract class BaseUnitTest extends TestCase
{
    protected $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }
}
