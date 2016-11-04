<?php

require_once('vendor/autoload.php');

$f3 = \Base::instance();

$f3->set('AUTOLOAD', 'app/');

$f3->config('app/config.ini');
$f3->config('app/routes.ini');
$f3->config('app/secrets.ini');

new Session();

$f3->run();
