<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;

class PaymentController extends Controller
{
    public function getPayments()
    {
        $payments = Payment::all();
        return [$payments];
    }

    public function fetchPayment($payment)
    {
        $payment = Payment::find($payment);
        return [
            'transactionID' => $payment->trsansactionID,
            'user_id' => $payment->user_id,
            'reservation_id' => $payment->reservation_id,
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at
        ];
    }

    public function deletePayment($payment)
    {
        Payment::find($payment)->delete();
        return;
    }
}
