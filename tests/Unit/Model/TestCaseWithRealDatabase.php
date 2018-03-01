<?php

namespace Tests\Unit\Model;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class TestCaseWithRealDatabase extends TestCase
{
    use RefreshDatabase;

    public function createApplication()
    {
        putenv('DB_CONNECTION=sqlite_testing');

        return parent::createApplication();
    }
}
