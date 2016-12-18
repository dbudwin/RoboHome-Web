<?php

namespace Tests\Models;

use DB\SQL;
use PHPUnit\Framework\TestCase;
use Tests\BaseUnitTest;

abstract class BaseModelTest extends BaseUnitTest
{
    private $connection;
    private $tempDatabaseFilename = './tests/temp.sqlite3';

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
