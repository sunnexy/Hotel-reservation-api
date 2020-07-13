<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\userPayment;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use App\Reservation;
use App\Room;
use App\User;
use App\Payment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\MakePayment;
use App\Notifications\NotifyUserDeposit;
use Illuminate\Support\Facades\Notification;

class ReservationController extends Controller
{
    use MakePayment;
    public function createReservation(Request $request, $room)
    {
        $this->validate($request, [
            'room_id' => ['required', 'numeric'],
            'arrive_at' => ['required'],
            'depart_at' => ['required']
        ]);

        $reservation = new Reservation;
        $reservation->user_id = $request->user()->id;
        $reservation->room_id = $room;
        $reservation->arrive_at = Carbon::parse($request->arrive_at);
        $reservation->depart_at = Carbon::parse($request->depart_at);
        $reservation->save();
        return;
    }

    public function getReservation($reservation)
    {
        $reservation = Reservation::find($reservation)->first();
        return [
            'user_id' => $reservation->user_id,
            'room_id' => $reservation->room_id,
            'arrive_at' => $reservation->arrive_at,
            'depart_at' => $reservation->depart_at
        ];
    }

    public function getReservations()
    {
        $reservations = Reservation::all();
        return [$reservations];
    }

    public function updateReservation(Request $request, $reservation)
    {
        $this->validate($request, [
            'room_id' => ['required', 'numeric'],
            'arrive_at' => ['required'],
            'depart_at' => ['required']
        ]);
        Reservation::find($reservation)->update($request->all());
        return;
    }

    public function verifyPayment(Request $request, $reservation, $reference)
    {
        //$result = $this->makePayment($reservation);
        $data = $this->makePayment($reservation);
        //$reference = $result['reference'];
        $payment = new Payment();
        $payment->transactionID = $reference;
        $payment->user_id = Auth::user()->id;
        $payment->reservation_id = $reservation;
        $payment->amount = $data['amount'];
        $payment->save();

        $vrl = "https://api.paystack.co/transaction/verify/$reference";
        $dh = curl_init();

        curl_setopt($dh, CURLOPT_URL, $vrl);
        curl_setopt($dh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($dh, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer sk_test_30f425758211360164214c9c98e171b94d1f0a37",
            "Cache-Control: no-cache",
        ));
        curl_setopt($dh,CURLOPT_RETURNTRANSFER, true);
        $check = curl_exec($dh);

        $verify = array();
        if($check){
            $verify = json_decode($check, true);
        }
        //abort_unless($data['amount'] == $result['data']['amount'], 422, 'Not processed');
        if($data['amount'] != $verify['data']['amount'])
        {
            abort(422, 'Error');
        }
        $payment->paid_at = Carbon::parse($request->paid_at);
        Payment::find($payment->id)->update($request->all());
        //dd($payment);;
        $payment = Payment::find($payment->id);
        $user = $payment->user;
        //dd($payment->reservation->room->room_no);
        $user->notify(new NotifyUserDeposit($payment));
        return [
            'message' => $verify['message'],
            'reference' => $verify['data']['reference'],
            'amount' => $verify['data']['amount'],
            'paid_at' => $payment->paid_at
        ];
    }

//    private function userPayment($user, $payment)
//    {
//        if (!is_null($user)) {
//            Notification::send($user, new NotifyUserDeposit($user, $payment));
//        }
//    }

    public function deleteReservation($reservation)
    {
        Reservation::find($reservation)->delete();
        return;
    }

    public function userCheckin($reservation)
    {
        $res = Reservation::find($reservation);
        $res->check_in = true;

        $res->room->IsEmpty = false;
        $res->push();
        return;
    }

    public function userCheckout($reservation)
    {
        $res = Reservation::find($reservation);
        $res->check_in = false;

        $res->room->IsEmpty = true;
        $res->push();
        return;
    }
}
