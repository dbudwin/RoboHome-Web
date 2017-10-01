<?php

namespace Tests\Unit\Model;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

abstract class ModelTestCase extends TestCase
{
    use DatabaseMigrations;

    public function createApplication()
    {
        putenv('DB_CONNECTION=sqlite_testing');

        return parent::createApplication();
    }

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:reset');

        parent::tearDown();
    }
}
