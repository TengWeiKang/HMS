<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function __construct() {
        $this->middleware("employee:admin,staff");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = Reservation::all();
    }

    /**
     * Return a listing of the resource with certain attributes.
     *
     * @return array
     */
    public function json(Request $request)
    {
        $roomID = $request->roomID;
        $reservations = Room::find($roomID)->reservations;
        $json = [];
        foreach ($reservations as $reservation) {
            $endDate = Carbon::createFromFormat('Y-m-d', $reservation->end_date);
            $json[] = [
                "start" => $reservation->start_date,
                "end" => $endDate->addDays(1)->format("Y-m-d"),
                "rendering" => "background",
                "className" => ["bg-red"]
            ];
        };
        return $json;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms = Room::all();
        $customers = Customer::all();
        return view('dashboard/reservation/create-form', ["rooms" => $rooms, "customers" => $customers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $split = explode("||", $request->customer, 2);
        $isCustomer = $split[0] == 'c' ? 1 : 0;
        $customerID = $split[1];
        if (!$isCustomer) {
            $customerID = Guest::create([
                "name" => $customerID
            ])->id;
        }
        $customerID = (int) $customerID;
        $validator = Validator::make($request->all(), [
            "startDate" => "required|date|after_or_equal:today",
            "endDate" => "required|date|after_or_equal:startDate"
        ]);
        $validator->after(function ($validator) use ($request) {
            $count = Reservation::where("room_id", $request->roomId)
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
            "room_id" => $request->roomId,
            "start_date" => $request->startDate,
            "end_date" => $request->endDate,
            "reservable_type" => $isCustomer ? Customer::class : Guest::class,
            "reservable_id" => $customerID,
            "check_in" => ($request->checkIn ? Carbon::now() : null)
        ]);

        return redirect()->route('dashboard.reservation.create')->with("message", "New Reservation Created Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        //
    }

    private function validReservationDate($startDate, $endDate) {

    }
}
