<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function createRoom(Request $request)
    {
        $this->validate($request, [
            'room_no' => ['required', 'numeric'],
            'roomType' => ['required', 'string'],
            'capacity' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
        ]);

        $room = new Room($request->all());
        $room->save();

        return;
    }

    public function getRooms()
    {
        $rooms = Room::all();
        return [$rooms];
    }

    public function getAvailableRooms()
    {
        $rooms = Room::where('IsEmpty', true)->get();
        return [$rooms];
    }

    public function getRoom($id)
    {
        $room = Room::find($id)->first();
        return [
            'room_no' => $room->room_no,
            'roomType' => $room->roomType,
            'capacity' => $room->capacity,
            'description' => $room->description,
            'amount' => $room->amount,
            'IsEmpty' => $room->IsEmpty
        ];

    }
    public function updateRoom(Request $request, $id)
    {
        $this->validate($request, [
            'room_no' => ['required', 'numeric'],
            'roomType' => ['required', 'string'],
            'capacity' => ['required', 'numeric'],
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
        ]);
        Room::find($id)->update($request->all());
        return;
    }

    public function deleteRoom($id)
    {
        $room = Room::find($id);
        $room->delete();
        return;
    }
}
