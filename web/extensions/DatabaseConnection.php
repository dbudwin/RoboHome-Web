<?php
    //Borrowed and modified from https://github.com/erangalp/database-tutorial
    class Database {
        protected static $connection;

        public function connect() {
            if (!isset(self::$connection)) {
                require("Credentials.php");
                self::$connection = new mysqli($MYSQL_SERVERNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD, $MYSQL_DBNAME);
            }

            if (self::$connection === false) {
                return false;
            }

            return self::$connection;
        }

        public function query($query) {
            $connection = $this->connect();
            $result = $connection->query($query);

            return $result;
        }

        public function select($query) {
            $connection = $this->connect();
            $rows = array();
            $result = $this->query($query);

            if ($result === false) {
                return false;
            }

            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }

            return $rows;
        }

        public function error() {
            $connection = $this->connect();
            return $connection->error;
        }

        public function quote($value) {
            $connection = $this->connect();
            return "'" . $connection->real_escape_string($value) . "'";
        }
    }
?>