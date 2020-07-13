<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Room;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Room::class, function (Faker $faker) {
    return [
        'room_no' => $faker->numberBetween(1,3000),
        'roomType' => $faker->lastName,
        'capacity' => 2,
        'description' => $faker->sentence,
        'amount' => 10000,
        'IsEmpty' => true
    ];
});
