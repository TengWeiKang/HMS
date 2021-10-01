<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Employee;

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
        $turnovers = $rooms->filter(function ($value) {
            return $value->isTurnoverToday();
        });
        $departures = $rooms->filter(function ($value) {
            return $value->isDepartureToday();
        });
        $arrivals = $rooms->filter(function ($value) {
            return $value->isArrivalToday();
        });
        $housekeepers = Employee::with("housekeepRooms")->where("role", 2)->get();
        return view('dashboard/housekeeper/index', ["turnovers" => $turnovers, "departures" => $departures, "arrivals" => $arrivals, "housekeepers" => $housekeepers]);
    }
}
