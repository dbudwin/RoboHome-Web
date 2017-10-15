<?php

use App\Http\Globals\DeviceTypes;
use Faker\Generator as Faker;

$factory->define(App\Device::class, function (Faker $faker) {
    return [
        'name' => $faker->word(),
        'description' => $faker->sentence(),
        'user_id' => function () {
            return factory(App\User::class)->make()->id;
        },
        'device_type_id' => DeviceTypes::RF_DEVICE
    ];
});
