<?php

namespace Tests\Models;

use DB\SQL;
use PHPUnit\Framework\TestCase;

abstract class BaseModelTest extends TestCase
{
    protected $faker = null;
    private $connection = null;
    private $tempDatabaseFilename = './tests/temp.sqlite3';

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    protected function tearDown()
    {
        unlink($this->tempDatabaseFilename);
    }

    final protected function getConnection()
    {
        if ($this->connection === null) {
            $fileCopied = copy('./tests/robohome.sqlite3', $this->tempDatabaseFilename);

            if ($fileCopied) {
                $this->connection = new SQL('sqlite:' . $this->tempDatabaseFilename);
            }
        }

        return $this->connection;
    }
}
