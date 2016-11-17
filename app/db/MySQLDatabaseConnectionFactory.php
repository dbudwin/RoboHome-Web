<?php

namespace DB;

class MySQLDatabaseConnectionFactory implements IDatabaseConnectionFactory
{
    public function createConnection(\Base $f3)
    {
        $mysqlServerName = $f3->get('MYSQL_SERVERNAME');
        $mysqlDatabaseName = $f3->get('MYSQL_DBNAME');

        return new \DB\SQL(
            "mysql:host=$mysqlServerName;port=3306;dbname=$mysqlDatabaseName",
            $f3->get('MYSQL_USERNAME'),
            $f3->get('MYSQL_PASSWORD')
        );
    }
}
