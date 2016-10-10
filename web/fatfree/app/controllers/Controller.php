<?php

namespace Controllers;

class Controller {
    protected $f3;
    protected $db;

    public function __construct()
    {
        $f3 = \Base::instance();
        $this->f3 = $f3;

        $mysqlServerName = $f3->get("MYSQL_SERVERNAME");
        $mysqlDatabseName = $f3->get("MYSQL_DBNAME");

        $db = new \DB\SQL(
            "mysql:host={$mysqlServerName};port=3306;dbname={$mysqlDatabseName}",
            $f3->get("MYSQL_USERNAME"),
            $f3->get("MYSQL_PASSWORD")
        );

        $this->db = $db;
    }

    function beforeRoute() {
        if ($this->f3->get("SESSION.user") === null ) {
            $this->f3->reroute("@loginPage");
            exit;
        }
    }

    function afterRoute() {
    }
}
