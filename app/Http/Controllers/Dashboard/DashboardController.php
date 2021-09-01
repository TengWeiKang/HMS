<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function __construct() {
        $this->middleware("employee:admin,staff");
    }

    public function index() {
        return view('dashboard/dashboard');
    }

    public function json(Request $request) {
        $return = $request->return;
        $json = [];
        if ($return === "resources") {
            $rooms = Room::with("reservations")->get();
            foreach ($rooms as $room) {
                $json[] = [
                    "id" => $room->id,
                    "room_id" => $room->room_id,
                    "title" => $room->room_id . " - " . $room->name . " (" . $room->status(false) . ")",
                    "price" => $room->price
                ];
            }
        }
        else if ($return === "events"){
            $reservations = Reservation::with("reservable", "payment")->get();
            foreach ($reservations as $reservation) {
                $json[] = [
                    "id" => $reservation->id,
                    "resourceId" => $reservation->room_id,
                    "backgroundColor" => $reservation->statusColor(),
                    "textColor" => "black",
                    "classNames" => "text-center",
                    "title" => $reservation->reservable->username,
                    "start" => $reservation->start_date->format("Y-m-d"),
                    "end" => $reservation->end_date->addDays()->format("Y-m-d"),
                    "editable" => ($reservation->statusName() == "Completed") ? false : true,
                    "resourceEditable" => ($reservation->statusName() == "Completed") ? false : true,
                ];
            }
        }
        return $json;
    }

    public function reservation_date_update(Request $request) {
        $reservation = Reservation::findOrFail($request->id);
        if ($request->has("room_id"))
            $reservation->room_id = $request->room_id;
        $reservation->start_date = $request->start_date;
        $reservation->end_date = $request->end_date;
        $reservation->save();
        return response()->json(['success' => "The reservation has been updated successfully"]);

    }
}
