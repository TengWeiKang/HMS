<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
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
            $roomTypes = RoomType::with(["rooms", "rooms.reservations"])->get();
            foreach ($roomTypes as $roomType) {
                if ($roomType->rooms->count() == 0)
                    continue;
                $data = [
                    "id" => $roomType->id * -1,
                    "title" => $roomType->name . " (RM " . number_format($roomType->price, 2) . ")",
                    "children" => [],
                    "editable" => false,
                ];
                foreach ($roomType->rooms as $room) {
                    $data["children"][] = [
                        "id" => $room->id,
                        "room_id" => $room->room_id,
                        "title" => $room->room_id . " - " . $room->name . " (" . $room->statusName(false) . ")",
                        "price" => $roomType->price,
                        "status" => $room->status()
                    ];
                }
                $json[] = $data;
            }
        }
        else if ($return === "events"){
            $start = new Carbon(explode("T", $request->start)[0]);
            $end = new Carbon(explode("T", $request->end)[0]);
            $end->subDay();
            $reservations = Reservation::with("customer", "payment")
                ->where("status", 1)
                ->where(function ($query) use ($start, $end) {
                $query->where("start_date", "<=", $start)
                    ->where("end_date", ">=", $start)
                    ->orWhere("start_date", "<=", $end)
                    ->where("end_date", ">=", $end)
                    ->orWhere("start_date", ">=", $start)
                    ->where("end_date", "<=", $end);
                }
            )->get();
            $isHousekeeper = Auth::guard("employee")->user()->isHousekeeper();
            foreach ($reservations as $reservation) {
                $json[] = [
                    "id" => $reservation->id,
                    "resourceId" => $reservation->room_id,
                    "backgroundColor" => $reservation->statusColor(),
                    "textColor" => "black",
                    "classNames" => "text-center event-pointer",
                    "title" => $reservation->customer->fullName(),
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
            $room = Room::findOrFail($request->room_id);
            if ($room->isCheckIn() == 4) {
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
