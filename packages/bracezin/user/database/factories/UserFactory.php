<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    $count = User::all()->count();

    return [
        'username' => (string) $faker->name.(string) $count,
        'email' => (string) $faker->unique()->safeEmail,
        'password' => Config('userConfig.default_user_password', 'secret'),
        'is_active' => $faker->randomElement([0, 1]),
        'is_email_verified' => $faker->randomElement([0, 1]),
    ];
});
