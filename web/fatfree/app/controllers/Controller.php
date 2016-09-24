<?php

class Controller {
    protected $f3;
    protected $db;

    function __construct() {
        $f3 = Base::instance();
        $this->f3 = $f3;

        $db = new DB\SQL("mysql:host=" . $f3->get("MYSQL_SERVERNAME") . ";port=3306;dbname=" . $f3->get("MYSQL_DBNAME"), $f3->get("MYSQL_USERNAME"), $f3->get("MYSQL_PASSWORD"));

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
