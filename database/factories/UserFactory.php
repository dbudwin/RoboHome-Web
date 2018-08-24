<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name(),
        'email' => $faker->unique()->safeEmail(),
        'email_verified_at' => $faker->dateTime(),
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'public_id' => $faker->uuid()
    ];
});
