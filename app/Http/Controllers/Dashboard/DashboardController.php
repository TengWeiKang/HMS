<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index() {
        return view('dashboard/dashboard');
    }

    public function json(Request $request) {
        $return = $request->return;
        $json = [];
        if ($return === "resources") {
            $roomtypes = RoomType::with(["rooms", "rooms.reservations"])->get();
            foreach ($roomtypes as $roomtype) {
                if ($roomtype->rooms->count() == 0)
                    continue;
                $data = [
                    "id" => $roomtype->id * -1,
                    "title" => $roomtype->name,
                    "children" => [],
                    "editable" => false,
                    "eventOverlap" => false,
                    "selectable" => false,
                ];
                foreach ($roomtype->rooms as $room) {
                    $data["children"][] = [
                        "id" => $room->id,
                        "room_id" => $room->room_id,
                        "title" => $room->room_id . " - " . $room->name . " (" . $room->statusName(false) . ")",
                        "price" => $room->price,
                        "status" => $room->status()
                    ];
                }
                $json[] = $data;
            }
        }
        else if ($return === "events"){
            $reservations = Reservation::with("reservable", "payment")->get();
            $isHousekeeper = Auth::guard("employee")->user()->isHousekeeper();
            foreach ($reservations as $reservation) {
                if ($reservation->status() == 3)
                    continue;
                $json[] = [
                    "id" => $reservation->id,
                    "resourceId" => $reservation->room_id,
                    "backgroundColor" => $reservation->statusColor(),
                    "textColor" => "black",
                    "classNames" => "text-center event-pointer",
                    "title" => $reservation->reservable->username,
                    "start" => $reservation->start_date->format("Y-m-d"),
                    "end" => $reservation->end_date->addDays()->format("Y-m-d"),
                    "editable" => ($reservation->status() == 2 || $isHousekeeper) ? false : true, //status 2 = completed
                    "resourceEditable" => ($reservation->status() == 2 || $isHousekeeper) ? false : true, //status 2 = completed
                    "totalPrice" => $reservation->finalPrices(),
                    "status" => $reservation->status(),
                    "paymentId" => optional($reservation->payment)->id,
                ];
            }
        }
        return $json;
    }

    public function reservation_date_update(Request $request) {
        $reservation = Reservation::findOrFail($request->id);
        if ($request->has("room_id")) {
            if ($reservation->isReserved() == 4) {
                $room = Room::findOrFail($request->room_id);
                $room->status = 0;
                $room->save();
            }
            $reservation->room_id = $request->room_id;
        }
        $reservation->start_date = $request->start_date;
        $reservation->end_date = $request->end_date;
        $reservation->save();
        return response()->json(['success' => "The reservation has been updated successfully"]);
    }
}
