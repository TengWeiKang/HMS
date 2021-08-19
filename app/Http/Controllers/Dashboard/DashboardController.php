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
            $rooms = Room::all();
            foreach ($rooms as $room) {
                $json[] = [
                    "id" => $room->id,
                    "title" => $room->room_id . " - " . $room->name,
                ];
            }
        }
        else {
            $reservations = Reservation::all();
            foreach ($reservations as $reservation) {
                $json[] = [
                    "id" => $reservation->id,
                    "resourceId" => $reservation->room_id,
                    "title" => $reservation->reservable->username,
                    "start" => $reservation->start_date,
                    "end" => Carbon::createFromFormat('Y-m-d', $reservation->end_date)->addDays(1)->format("Y-m-d")
                ];
            }
        }
        return $json;
    }
}
