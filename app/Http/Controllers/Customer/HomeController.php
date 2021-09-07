<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        $rooms = Room::all();
        return view("customer/index", ["rooms" => $rooms]);
    }

    public function search(Request $request) {
        $rooms = Room::with("reservations")->get();
        $arrival = "";
        $departure = "";
        if (!empty($request->arrival) && empty($request->departure)) {
            $arrival = $departure = new Carbon($request->arrival);
        }
        else if (empty($request->arrival) && !empty($request->departure)) {
            $arrival = $departure = new Carbon($request->departure);
        }
        else if (!empty($request->arrival) && !empty($request->departure)){
            $arrival = new Carbon($request->arrival);
            $departure = new Carbon($request->departure);
        }
        $rooms = $rooms->filter(function ($value, $key) use ($request, $arrival, $departure) {
            if (!empty($arrival) && !empty($departure)) {
                $reservations = $value->reservations->filter(function ($value2, $key) use ($arrival, $departure) {
                    if ($value2->start_date->lte($arrival) && $value2->end_date->gte($arrival) ||
                    $value2->start_date->lte($departure) && $value2->end_date->gte($departure) ||
                    $value2->start_date->gte($arrival) && $value2->end_date->lte($departure)) {
                        return true;
                    }
                    return false;
                });
                if ($reservations->count() > 0)
                    return false;
            }
            if (!empty($request->single) && $value->single_bed != $request->single) {
                return false;
            }
            if (!empty($request->double) && $value->double_bed != $request->double) {
                return false;
            }
            if (!empty($request->price) && $request->price < $value->price) {
                return false;
            }
            return true;
        });
        // dd($rooms);
        return view("customer/components/accomodations", ["rooms" => $rooms]);
    }
}
