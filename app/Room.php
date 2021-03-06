<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['room_no', 'roomType', 'capacity', 'description', 'amount', 'IsEmpty'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
