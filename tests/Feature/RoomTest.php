<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Room;
use Faker\Factory;

class RoomTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function testCreateRoom()
    {
        $faker  = Factory::create();
        $data = [
            'room_no' => 20,
            'roomType' => $faker->userName,
            'capacity' =>4,
            'description' =>  $faker->sentence,
            'amount' => '10000'
        ];

        $response = $this->post('api/room/create', $data);
        $response->assertStatus(200);
    }

    public function testGetRooms()
    {
        \factory(Room::class)->create();
        $response = $this->get('api/room/get_all_rooms');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            ['*' =>
                [
                    'room_no',
                    'roomType',
                    'capacity',
                    'description',
                    'amount',
                    'IsEmpty'
                ]
            ]
        ]);
    }

    public function testGetAvailableRooms()
    {
        \factory(Room::class)->create();
        $response = $this->get('api/room/get_available_rooms');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            ['*' =>
                [
                    'room_no',
                    'roomType',
                    'capacity',
                    'description',
                    'amount',
                    'IsEmpty'
                ]
            ]
        ]);
    }

    public function testGetRoom()
    {
        $room = $room = \factory(Room::class)->create();
        $room = Room::find(1);
        $response = $this->get("api/room/get_a_room/$room->id");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'room_no',
            'roomType',
            'capacity',
            'description',
            'amount',
            'IsEmpty'
        ]);
    }

    public function testUpdateRoom()
    {
        $faker = Factory::create();
        $room = $room = \factory(Room::class)->create();
        $room = Room::find(1);
        $data = [
            'room_no' => 200,
            'roomType' => 'stellarr',
            'capacity' =>3,
            'description' =>  $faker->sentence,
            'amount' => '12000'
        ];
        $response = $this->patch("api/room/$room->id", $data);
        $response->assertStatus(200);
    }

    public function testDeleteRoom()
    {
        //$this->createRoom();
        $room = \factory(Room::class)->create();
        $response = $this->delete("api/room/$room->id");
        $response->assertStatus(200);
        $room = Room::find($room->id);
        $this->assertNull($room);
    }

    private function createRoom()
    {
        $faker = Factory::create();

        $room = new Room();
        $room->room_no = '201';
        $room->roomType = $faker->userName;
        $room->capacity = '1';
        $room->description = $faker->sentence;
        $room->amount = '10000';
        $room->IsEmpty = true;
        $room->save();

        $room = new Room();
        $room->room_no = '202';
        $room->roomType = $faker->userName;
        $room->capacity = '2';
        $room->description = $faker->sentence;
        $room->amount = '5000';
        $room->IsEmpty = false;
        $room->save();
    }
}
