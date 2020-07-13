<?php

namespace Tests\Feature;

use App\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Payment;
use App\User;
use App\Room;
use Faker\Factory;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Traits\MakePayment;

class PaymentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use MakePayment;
    use RefreshDatabase;
    public function testMakeAndVerifyPayment()
    {
        $faker = Factory::create();
//        $this->createPayment();
//        $payment = Payment::find(1);
        $this->createReservation();
        $reservation = Reservation::find(1);
        //dd($reservation);
        $result = $this->makePayment($reservation->id);
        $reference = $result['reference'];

        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $data = [
            'paid_at' => Carbon::now()
        ];
        $response = $this->patch("api/reservation/$reservation->id/verifyPayment/$reference", $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'reference',
            'amount',
            'paid_at'
        ]);
        $transactionID = Payment::find(1)->transactionID;
        $amount = Payment::find(1)->amount;
        $paid_at = Payment::find(1)->paid_at;
        $user->refresh();
        $this->assertEquals($reference, $transactionID);
        $this->assertNotNull($amount);
        $this->assertNotNull($paid_at);
    }

    public function testGetPayments()
    {
        $this->createPayment();
        $payment = Payment::all();

        $response = $this->get("api/payments");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            ['*' =>
                [
                    'transactionID',
                    'user_id',
                    'reservation_id',
                    'amount',
                    'paid_at'
                ]
            ]
        ]);
    }

    public function testGetAPayment()
    {
        $this->createPayment();
        $payment = Payment::find(1);

        $response = $this->get("api/payments/$payment->id");
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'transactionID',
            'user_id',
            'reservation_id',
            'amount',
            'paid_at'
        ]);
    }

    public function testDeletePayment()
    {
        $this->createPayment();
        $payment = Payment::find(1);

        $response = $this->delete("api/payments/$payment->id");
        $response->assertStatus(200);
        $payment = Payment::find($payment->id);
        $this->assertNull($payment);
    }

    private function createPayment()
    {
        $faker = Factory::create();
        $this->createReservation();
        $reservation = Reservation::find(1);
        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $payment = new Payment();
        $payment->transactionID = 'GH'.strtoupper(Str::random(9));
        $payment->user_id = $reservation->user_id;
        $payment->reservation_id = $reservation->id;
        $payment->amount = $reservation->room->amount;
        $payment->paid_at = Carbon::now();
        $payment->save();

        $payment = new Payment();
        $payment->transactionID = 'GH'.strtoupper(Str::random(9));
        $payment->user_id = 3;
        $payment->reservation_id = 5;
        $payment->amount = Reservation::find(2)->room->amount;
        $payment->paid_at = Carbon::now();
        $payment->save();
        //dd($reservation);
    }

    private function createReservation()
    {
        $user = \factory(User::class)->create();
        $this->actingAs($user);
        $room = \factory(Room::class)->create();

        $faker = Factory::create();
        //$this->createRoom();
        //$room = Room::find(1);
        $reservation = new Reservation();
        $reservation->user_id= $user->id;
        $reservation->room_id= $room->id;
        $reservation->arrive_at= Carbon::parse($faker->date());
        $reservation->depart_at= Carbon::parse($faker->date());
        $reservation->save();
        //dd($reservation);

        //$rope = Room::find(2);
        $reservation = new Reservation();
        $reservation->user_id= $user->id;
        $reservation->room_id= $room->id;
        $reservation->arrive_at= Carbon::parse($faker->date());
        $reservation->depart_at= Carbon::parse($faker->date());
        $reservation->save();
        //dd($reservation);
    }

    private function createRoom()
    {
        $faker = Factory::create();

        $room = new Room();
        $room->room_no = 200;
        $room->roomType = $faker->userName;
        $room->capacity = '1';
        $room->description = $faker->sentence;
        $room->amount = '10000';
        $room->IsEmpty = true;
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
}
