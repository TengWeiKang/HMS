<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware("customer");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function create(Room $room)
    {
        $room->load("reservations");
        return view("customer.booking.create-form", ["room" => $room]);
    }

    public function json(Request $request)
    {
        $roomID = $request->roomID;
        $reservations = Room::find($roomID)->reservations;
        if ($request->has("ignoreID")) {
            $ignoreID = $request->ignoreID;
            $reservations = $reservations->filter(function ($value, $key) use ($ignoreID) {
                return $value->id != $ignoreID;
            });
        }
        $json = [];
        foreach ($reservations as $reservation) {
            $json[] = [
                "start" => $reservation->start_date->format("Y-m-d"),
                "end" => $reservation->end_date->addDays(1)->format("Y-m-d"),
                "rendering" => "background",
                "className" => ["bg-red"],
                "checkin" => $reservation->check_in,
                "checkout" => $reservation->check_out,
            ];
        }
        return $json;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            "startDate" => "required|date|after_or_equal:today",
            "endDate" => "required|date|after_or_equal:startDate",
        ]);
        $validator->validate();
        $validator->after(function ($validator) use ($request, $room) {
            $count = Reservation::where("room_id", $room->id)
            ->where(function ($query) use ($request) {
                $query->where("start_date", "<=", $request->startDate)
                    ->where("end_date", ">=", $request->startDate)
                    ->orWhere("start_date", "<=", $request->endDate)
                    ->where("end_date", ">=", $request->endDate)
                    ->orWhere("start_date", ">=", $request->startDate)
                    ->where("end_date", "<=", $request->endDate);
                }
            )->count();
            if ($count > 0) {
                $validator->errors()->add("dateConflict", "The booking date has conflict with others booking");
            }
        });
        $validator->validate();

        Reservation::create([
            "room_id" => $room->id,
            "start_date" => $request->startDate,
            "end_date" => $request->endDate,
            "reservable_type" => Customer::class,
            "reservable_id" => Auth::user()->id,
        ]);
        return redirect()->route('customer.booking.create', ["room" => $room])->with("message", "New Reservation Created Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }
}
