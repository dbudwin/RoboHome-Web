<?php

use Interop\Container\ContainerInterface;

return [
    Base::class => function () {
        return \Base::instance();
    },
    DevicesModel::class => function (ContainerInterface $container) {
        return new \Models\DevicesModel($container->get(SQL::class));
    },
    RFDeviceModel::class => function (ContainerInterface $container) {
        return new \Models\RFDeviceModel($container->get(SQL::class));
    },
    UserDevicesModel::class => function (ContainerInterface $container) {
        return new \Models\UserDevicesModel($container->get(SQL::class));
    },
    UserDevicesViewModel::class => function (ContainerInterface $container) {
        return new \Models\UserDevicesViewModel($container->get(SQL::class));
    },
    UserModel::class => function (ContainerInterface $container) {
        return new \Models\UserModel($container->get(SQL::class));
    },
    SQL::class => DI\factory([\DB\MySQLDatabaseConnectionFactory::class, 'createConnection']),
];
