<?php

$f3 = require("lib/base.php");
$f3->config("app/config.ini");
$f3->config("app/routes.ini");
$f3->config("app/secrets.ini");

new Session();

$f3->run();
