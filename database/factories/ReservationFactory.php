<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Reservation;
use App\User;
use App\Room;
use Faker\Generator as Faker;
use Carbon\Carbon;

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

$factory->define(Reservation::class, function (Faker $faker) {
    return [
        'id' => 1,
        'user_id' => 1,
        'room_id' => \factory(Room::class)->create()->id,
        'arrive_at' => Carbon::parse($faker->date()),
        'depart_at' => Carbon::parse($faker->date()),
        'check_in' => false
    ];
});
