<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class HousekeeperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::with("reservations", "housekeeper")->get();
        $turnovers = $rooms->filter(function ($value, $key) {
            return $value->isTurnoverToday();
        });
        $departures = $rooms->filter(function ($value, $key) {
            return $value->isDepartureToday();
        });
        $arrivals = $rooms->filter(function ($value, $key) {
            return $value->isArrivalToday();
        });
        return view('dashboard/housekeeper/index', ["turnovers" => $turnovers, "departures" => $departures, "arrivals" => $arrivals]);
    }
}
