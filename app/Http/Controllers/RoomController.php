<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Patient;
use App\Models\Reservation;

class RoomController extends Controller
{
    public function checkInPatient(Request $request)
    {
        $room = Room::findOrFail($request->room_id);
        $patient = Patient::findOrFail($request->patient_id);

        if ($room->is_available) {
            Reservation::create([
                'room_id' => $room->id,
                'patient_id' => $patient->id,
                'check_in_date' => now(),
            ]);

            $room->update(['is_available' => false]);
        } else {
            return response()->json(['message' => 'Room is not available'], 400);
        }

        return response()->json(['message' => 'Patient checked in successfully']);
    }

    public function checkOutPatient(Request $request)
    {
        $reservation = Reservation::where('room_id', $request->room_id)
                                    ->where('patient_id', $request->patient_id)
                                    ->whereNull('check_out_date')
                                    ->firstOrFail();

        $reservation->update(['check_out_date' => now()]);

        $room = Room::findOrFail($request->room_id);
        $room->update(['is_available' => true]);

        return response()->json(['message' => 'Patient checked out successfully']);
    }
}
