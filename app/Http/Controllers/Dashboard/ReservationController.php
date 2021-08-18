<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Customer;
use App\Models\Guest;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $reservationList = Room::find($roomID)->reservations->toArray();
        $json = [];
        foreach ($reservationList as $value) {
            $endDate = Carbon::createFromFormat('Y-m-d', $value["end_date"]);
            $json[] = [
                "start" => $value["start_date"],
                "end" => $endDate->addDays(1)->format("Y-m-d"),
                "rendering" => "background",
                "className" => ["bg-red"]
            ];
        };
        return json_encode($json);
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
        //! TODO: validate two date inputs
        $split = explode("||", $request->customer, 2);
        $isCustomer = $split[0] == 'c' ? 1 : 0;
        $customerID = $split[1];
        if (!$isCustomer) {
            $customerID = Guest::create([
                "name" => $customerID
            ])->id;
        }
        $customerID = (int) $customerID;
        $this->validate($request, [
            "startDate" => "required|date|after_or_equal:yesterday",
            "endDate" => "required|date|after_or_equal:startDate"
        ]);
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
}
