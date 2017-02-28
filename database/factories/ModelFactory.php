<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

// Event
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Event::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->paragraph(3, true),
        'date' => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
    ];
});

// Service
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Service::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'cost' => $faker->randomFloat(2, 1.00, 1000.00)
    ];
});

// ServiceTag
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\ServiceTag::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name
    ];
});
