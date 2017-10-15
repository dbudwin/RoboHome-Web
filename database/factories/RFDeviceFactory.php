<?php

use Faker\Generator as Faker;

$factory->define(App\RFDevice::class, function (Faker $faker) {
    return [
        'on_code' => $faker->randomNumber(),
        'off_code' => $faker->randomNumber(),
        'pulse_length' => $faker->randomNumber(),
        'device_id' => function () {
            return factory(App\Device::class)->make()->id;
        },
    ];
});
