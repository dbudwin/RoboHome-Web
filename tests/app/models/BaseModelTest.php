<?php

namespace Test\Models;

use PHPUnit\Framework\TestCase;
use DB\SQL;

abstract class BaseModelTest extends TestCase
{
    private $connection = null;

    final public function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = new SQL($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWORD']);
            $this->connection->exec(file_get_contents('./schema.sql'));
        }

        return $this->connection;
    }
}

