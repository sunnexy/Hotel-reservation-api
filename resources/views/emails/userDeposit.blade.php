Dear {{ $payment->user->username }}, Your deposit of {{ $payment->amount }} has been credited on {{ $payment->paid_at }}. You can lodge in room {{ $payment->reservation->room->room_no }}
Thanks