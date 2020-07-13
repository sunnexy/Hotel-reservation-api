<?php
/**
 * Created by PhpStorm.
 * User: Master
 * Date: 7/8/2020
 * Time: 9:27 AM
 */

namespace App\Traits;
use App\Reservation;
use Illuminate\Support\Str;


trait MakePayment
{
    public function makePayment($reservation)
    {
        $url = "https://api.paystack.co/transaction/initialize";

        $reservation = Reservation::find($reservation)->first();
        $data = [
            'amount' => $reservation->room->amount,
            'email' => $reservation->user->email,
            'reference' => 'GH'.strtoupper(Str::random(10))
        ];

        $fields_string = http_build_query($data);

        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer sk_test_30f425758211360164214c9c98e171b94d1f0a37",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        //execute post
        $pay = curl_exec($ch);
        $result = array();
        if($pay){
            $result = json_decode($pay, true);
        }

        $reference = $result['data']['reference'];
        $amount = $data['amount'];
        return [
            'reference' => $result['data']['reference'],
            'amount' => $data['amount']
        ];
    }
}