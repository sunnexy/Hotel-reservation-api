<?php

namespace Tests\Feature;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Reservation;
use App\User;
use App\Room;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function testCreateReservation()
    {
        $faker = Factory::create();
        //$this->createRoom();
        $room = \factory(Room::class)->create();
        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $data = [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'arrive_at' => Carbon::parse($faker->date()),
            'depart_at' => Carbon::parse($faker->date())
        ];
        $response = $this->post("api/reservation/$room->id/reserve", $data);
        $response->assertStatus(200);
        $reservation    =   Reservation::where('room_id',$data['room_id'])->first();
        $this->assertNotNull($reservation);
    }

    public function testGetAReservation()
    {
        $this->createReservation();
        $reservation = Reservation::find(1);
        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $response = $this->get("api/reservation/$reservation->id");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user_id',
            'room_id',
            'arrive_at',
            'depart_at'
        ]);
    }

    public function testGetReservations()
    {
        $this->createReservation();
        $reservation = Reservation::all();
        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $response = $this->get("api/reservation/getReservations");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            ['*' =>
                [
                    'user_id',
                    'room_id',
                    'arrive_at',
                    'depart_at'
                ]
            ]
        ]);
    }

    public function testUpdateReservation()
    {
        $faker = Factory::create();
        $this->createReservation();
        $reservation = Reservation::find(2);
        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $data = [
            'room_id' => 2,
            'arrive_at' => Carbon::parse($faker->date()),
            'depart_at' => Carbon::parse($faker->date())
        ];
        $response = $this->patch("api/reservation/$reservation->id", $data);
        $response->assertStatus(200);
    }

    public function testDeleteReservation()
    {
        $this->createReservation();

        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $reservation = Reservation::find(1);
        $response = $this->delete("api/reservation/$reservation->id");
        $response->assertStatus(200);
        $reservation = Reservation::find($reservation->id);
        $this->assertNull($reservation);
    }

    public function testUserCheckin()
    {
        //$this->createReservation();
        $reservation = \factory(Reservation::class)->create();
        //$reservation = Reservation::find(1);
//        $data = [
//            'check_in' => False,
//            'IsEmpty' => False
//        ];
        $response = $this->patch("api/reservation/$reservation->id/checkin");
        $response->assertStatus(200);
        $reservation = Reservation::find(1);
        $this->assertEquals($reservation->check_in, true);
        $this->assertEquals($reservation->room->IsEmpty, false);
    }

    public function testUserCheckout()
    {
        //$reservation = \factory(Reservation::class)->create();
        $this->createReservation();
        $reservation = Reservation::find(1);

        $response = $this->patch("api/reservation/$reservation->id/checkout");
        $response->assertStatus(200);
        $reservation = Reservation::find(1);
        $this->assertEquals($reservation->check_in, false);
        $this->assertEquals($reservation->room->IsEmpty, true);
    }

    private function createRoom()
    {
        $faker = Factory::create();

        $room = new Room();
        $room->room_no = 202;
        $room->roomType = $faker->userName;
        $room->capacity = '1';
        $room->description = $faker->sentence;
        $room->amount = '10000';
        $room->IsEmpty = false;
        $room->save();

        $room = new Room();
        $room->room_no = 201;
        $room->roomType = $faker->userName;
        $room->capacity = '2';
        $room->description = $faker->sentence;
        $room->amount = '5000';
        $room->IsEmpty = false;
        $room->save();
    }

    private function createReservation()
    {
        $faker = Factory::create();
        $room = \factory(Room::class)->create();
        $reservation = new Reservation();
        $reservation->user_id= 1;
        $reservation->room_id= $room->id;
        $reservation->arrive_at= Carbon::parse($faker->date());
        $reservation->depart_at= Carbon::parse($faker->date());
        $reservation->check_in = true;
        $reservation->save();

        $reservation = new Reservation();
        $reservation->user_id= 2;
        $reservation->room_id= $room->id;
        $reservation->arrive_at= Carbon::parse($faker->date());
        $reservation->depart_at= Carbon::parse($faker->date());
        $reservation->save();

    }
}
