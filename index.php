<?php

require_once('vendor/autoload.php');

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(__DIR__ . '/app/di/definitions.php');
$builder->useAnnotations(false);

$container = $builder->build();

$f3 = $container->get(Base::class);

$f3->set('container', $container);
$f3->set('AUTOLOAD', 'app/');

$f3->config('app/config.ini');
$f3->config('app/routes.ini');
$f3->config('app/secrets.ini');

new Session();

$f3->run();
