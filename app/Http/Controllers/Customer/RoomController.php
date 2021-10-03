<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function show(RoomType $roomType, $singleBed, $doubleBed)
    {
        if (request()->has(["startDate", "endDate"])) {
            $startDate = request()->startDate;
            $endDate = request()->endDate;
            $rooms = $this->roomFilterAvailable($roomType, $singleBed, $doubleBed, $startDate, $endDate);
            // dd($rooms);
            return view('customer.room.view', [
                "roomType" => $roomType, 
                "singleBed" => $singleBed, 
                "doubleBed" => $doubleBed, 
                "startDate" => request()->startDate, 
                "endDate" => request()->endDate,
                "count" => $rooms->count()
            ]);
        }
        return view('customer.room.view', [
            "roomType" => $roomType, 
            "singleBed" => $singleBed, 
            "doubleBed" => $doubleBed, 
        ]);
    }

    public function roomFilterAvailable($roomType, $singleBed, $doubleBed, $startDate, $endDate) {
        $arrival = new Carbon($startDate);
        $departure = new Carbon($endDate);
        $rooms = Room::with("reservations", "type")
            ->where("room_type", $roomType->id)
            ->where("single_bed", $singleBed)
            ->where("double_bed", $doubleBed)
            ->get();
        $rooms = $rooms->filter(function ($value) use ($arrival, $departure) {
            if (!empty($arrival) && !empty($departure)) {
                $reservations = $value->reservations->filter(function ($value2) use ($arrival, $departure) {
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
            return true;
        });
        return $rooms;
    }
}
