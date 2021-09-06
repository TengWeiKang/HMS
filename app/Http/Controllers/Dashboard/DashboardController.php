<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\Reservation;
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
            $rooms = Room::with("reservations")->get();
            foreach ($rooms as $room) {
                $json[] = [
                    "id" => $room->id,
                    "room_id" => $room->room_id,
                    "title" => $room->room_id . " - " . $room->name . " (" . $room->statusName(false) . ")",
                    "price" => $room->price,
                    "status" => $room->status()
                ];
            }
        }
        else if ($return === "events"){
            $reservations = Reservation::with("reservable", "payment")->get();
            $isHousekeeper = Auth::guard("employee")->user()->isHousekeeper();
            foreach ($reservations as $reservation) {
                $json[] = [
                    "id" => $reservation->id,
                    "resourceId" => $reservation->room_id,
                    "backgroundColor" => $reservation->statusColor(),
                    "textColor" => "black",
                    "classNames" => "text-center event-pointer",
                    "title" => $reservation->reservable->username,
                    "start" => $reservation->start_date->format("Y-m-d"),
                    "end" => $reservation->end_date->addDays()->format("Y-m-d"),
                    "editable" => ($reservation->statusName() == "Completed" || $isHousekeeper) ? false : true,
                    "resourceEditable" => ($reservation->statusName() == "Completed" || $isHousekeeper) ? false : true,
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
        if ($request->has("room_id"))
            $reservation->room_id = $request->room_id;
        $reservation->start_date = $request->start_date;
        $reservation->end_date = $request->end_date;
        $reservation->save();
        return response()->json(['success' => "The reservation has been updated successfully"]);

    }
}
