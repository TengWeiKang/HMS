<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $roomTypes = RoomType::with("rooms")->get()->filter(function ($value, $key) {
            return $value->rooms->count() > 0;
        });
        return view("customer/index", ["roomTypes" => $roomTypes]);
    }

    public function search(Request $request) {
        $arrival = new Carbon($request->arrival);
        $departure = new Carbon($request->departure);
        $rooms = Room::with("reservations", "type")->get();
        $rooms = $rooms->filter(function ($room) use ($request, $arrival, $departure) {
            if (!empty($arrival) && !empty($departure)) {
                $reservations = $room->reservations->filter(function ($reservation) use ($arrival, $departure) {
                    if ($reservation->start_date->lte($arrival) && $reservation->end_date->gte($arrival) ||
                    $reservation->start_date->lte($departure) && $reservation->end_date->gte($departure) ||
                    $reservation->start_date->gte($arrival) && $reservation->end_date->lte($departure)) {
                        return true;
                    }
                    return false;
                });
                if ($reservations->count() > 0)
                    return false;
            }
            if (!empty($request->single) && $room->single_bed != $request->single) {
                return false;
            }
            if (!empty($request->double) && $room->double_bed != $request->double) {
                return false;
            }
            if (!empty($request->roomType) && $request->roomType != $room->type->id) {
                return false;
            }
            if (!empty($request->person) && $request->person != $room->single_bed + $room->double_bed * 2) {
                return false;
            }
            return true;
        });
        $rooms = $rooms->groupBy(["type.name", "single_bed", "double_bed"]);
        return view("customer/components/accomodations", ["roomGroups" => $rooms, "startDate" => $request->arrival, "endDate" => $request->departure]);
    }
}
